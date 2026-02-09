<?php
namespace ObzoraNMS\Data\Source;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Exceptions\FpingUnparsableLine;
use Log;
use Symfony\Component\Process\Process;

class Fping
{
    private string $fping_bin;
    private string|false $fping6_bin;
    private int $count;
    private int $timeout;
    private int $interval;
    private int $tos;
    private int $retries;

    public function __construct()
    {
        // prep fping parameters
        $this->fping_bin = ObzoraConfig::get('fping', 'fping');
        $fping6 = ObzoraConfig::get('fping6', 'fping6');
        $this->fping6_bin = is_executable($fping6) ? $fping6 : false;
        $this->count = max(ObzoraConfig::get('fping_options.count', 3), 1);
        $this->interval = max(ObzoraConfig::get('fping_options.interval', 500), 20);
        $this->timeout = max(ObzoraConfig::get('fping_options.timeout', 500), $this->interval);
        $this->retries = ObzoraConfig::get('fping_options.retries', 2);
        $this->tos = ObzoraConfig::get('fping_options.tos', 0);
    }

    /**
     * Run fping against a hostname/ip in count mode and collect stats.
     *
     * @param  string  $host  hostname or ip
     * @param  string  $address_family  ipv4 or ipv6
     * @return FpingResponse
     */
    public function ping($host, $address_family = 'ipv4'): FpingResponse
    {
        if ($address_family == 'ipv6') {
            $cmd = $this->fping6_bin === false ? [$this->fping_bin, '-6'] : [$this->fping6_bin];
        } else {
            $cmd = $this->fping6_bin === false ? [$this->fping_bin, '-4'] : [$this->fping_bin];
        }

        // build the command
        $cmd = array_merge($cmd, [
            '-e',
            '-q',
            '-c',
            $this->count,
            '-p',
            $this->interval,
            '-t',
            $this->timeout,
            '-O',
            $this->tos,
            $host,
        ]);

        $process = app()->make(Process::class, ['command' => $cmd]);
        Log::debug('[FPING] ' . $process->getCommandLine() . PHP_EOL);
        $process->run();

        $response = FpingResponse::parseLine($process->getErrorOutput(), $process->getExitCode());

        Log::debug("response: $response");

        return $response;
    }

    public function bulkPing(array $hosts, callable $callback): void
    {
        $process = app()->make(Process::class, ['command' => [
            $this->fping_bin,
            '-f', '-',
            '-e',
            '-t', $this->timeout,
            '-r', $this->retries,
            '-O', $this->tos,
            '-c', $this->count,
        ]]);

        // twice polling interval
        $process->setTimeout(ObzoraConfig::get('rrd.step', 300) * 2);
        // send hostnames to stdin to avoid overflowing cli length limits
        $process->setInput(implode(PHP_EOL, $hosts) . PHP_EOL);

        Log::debug('[FPING] ' . $process->getCommandLine() . PHP_EOL);

        $partial = '';
        $process->run(function ($type, $output) use ($callback, &$partial) {
            // stdout contains individual ping responses, stderr contains summaries
            if ($type == Process::ERR) {
                $lines = explode(PHP_EOL, $output);
                foreach ($lines as $index => $line) {
                    if ($line) {
                        Log::debug("Fping OUTPUT|$line PARTIAL|$partial");
                        try {
                            $response = FpingResponse::parseLine($partial . $line);
                            call_user_func($callback, $response);
                            $partial = '';
                        } catch (FpingUnparsableLine $e) {
                            // handle possible partial line (only save it if it is the last line of output)
                            $partial = $index === array_key_last($lines) ? $e->unparsedLine : '';
                        }
                    }
                }
            }
        });
    }
}
