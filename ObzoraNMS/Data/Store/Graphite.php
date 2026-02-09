<?php
namespace ObzoraNMS\Data\Store;

use App\Facades\ObzoraConfig;
use App\Polling\Measure\Measurement;
use Carbon\Carbon;
use Log;

class Graphite extends BaseDatastore
{
    protected $connection;

    protected $prefix;

    public function __construct(\Socket\Raw\Factory $socketFactory)
    {
        parent::__construct();
        $host = ObzoraConfig::get('graphite.host');
        $port = ObzoraConfig::get('graphite.port', 2003);
        try {
            if (self::isEnabled() && $host && $port) {
                $this->connection = $socketFactory->createClient("$host:$port");
            }
        } catch (\Exception $e) {
            d_echo($e->getMessage());
        }

        if ($this->connection) {
            Log::notice("Graphite connection made to $host");
        } else {
            Log::error("Graphite connection to $host has failed!");
        }

        $this->prefix = ObzoraConfig::get('graphite.prefix', '');
    }

    public function getName(): string
    {
        return 'Graphite';
    }

    public static function isEnabled(): bool
    {
        return ObzoraConfig::get('graphite.enable', false);
    }

    /**
     * @inheritDoc
     */
    public function write(string $measurement, array $fields, array $tags = [], array $meta = []): void
    {
        if (! $this->connection) {
            d_echo("Graphite Error: not connected\n");

            return;
        }

        $timestamp = Carbon::now()->timestamp;

        if ($measurement == 'ports') {
            $measurement = 'ports|' . $tags['ifName'];
        }

        $hostname = $this->getDevice($meta)->hostname;

        // metrics will be built as prefix.hostname.measurement.field value timestamp
        // metric fields can not contain . as this is used by graphite as a field separator
        $hostname = preg_replace('/\./', '_', (string) $hostname);
        $measurement = preg_replace(['/\./', '/\//'], '_', $measurement);
        $measurement = preg_replace('/\|/', '.', $measurement);

        $measurement_name = preg_replace('/\./', '_', $meta['rrd_name'] ?? ''); // FIXME don't use rrd_name
        $ms_name = is_array($measurement_name) ? implode('.', $measurement_name) : $measurement_name;
        // remove the port-id tags from the metric
        if (preg_match('/^port-id\d+/', $ms_name)) {
            $ms_name = '';
        }

        foreach ($fields as $k => $v) {
            // Skip fields without values
            if (is_null($v)) {
                continue;
            }
            $metric = implode('.', array_filter([$this->prefix, $hostname, $measurement, $ms_name, $k]));
            $this->writeData($metric, $v, $timestamp);
        }
    }

    /**
     * @param  string  $metric
     * @param  mixed  $value
     * @param  mixed  $timestamp
     */
    private function writeData($metric, $value, $timestamp)
    {
        try {
            $stat = Measurement::start('write');

            // Further sanitize the full metric before sending, whitespace isn't allowed
            $metric = preg_replace('/\s+/', '_', $metric);

            $line = implode(' ', [$metric, $value, $timestamp]);
            Log::debug("Sending to Graphite: $line\n");
            $this->connection->write("$line\n");

            $this->recordStatistic($stat->end());
        } catch (\Socket\Raw\Exception $e) {
            Log::error('Graphite write error: ' . $e->getMessage());
        }
    }
}
