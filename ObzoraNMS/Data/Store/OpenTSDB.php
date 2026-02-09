<?php
namespace ObzoraNMS\Data\Store;

use App\Facades\ObzoraConfig;
use App\Polling\Measure\Measurement;
use Carbon\Carbon;
use Log;

class OpenTSDB extends BaseDatastore
{
    /** @var \Socket\Raw\Socket */
    protected $connection;

    public function __construct(\Socket\Raw\Factory $socketFactory)
    {
        parent::__construct();
        $host = ObzoraConfig::get('opentsdb.host');
        $port = ObzoraConfig::get('opentsdb.port', 2181);
        try {
            if (self::isEnabled() && $host && $port) {
                $this->connection = $socketFactory->createClient("$host:$port");
            }
        } catch (\Socket\Raw\Exception $e) {
            Log::debug('OpenTSDB Error: ' . $e->getMessage());
        }

        if ($this->connection) {
            Log::notice('Connected to OpenTSDB');
        } else {
            Log::error('Connection to OpenTSDB has failed!');
        }
    }

    public function getName(): string
    {
        return 'OpenTSDB';
    }

    /**
     * @inheritDoc
     */
    public function write(string $measurement, array $fields, array $tags = [], array $meta = []): void
    {
        if (! $this->connection) {
            Log::error("OpenTSDB Error: not connected\n");

            return;
        }

        $timestamp = Carbon::now()->timestamp;
        $tmp_tags = 'hostname=' . $this->getDevice($meta)->hostname;

        foreach ($tags as $k => $v) {
            $v = str_replace([' ', ',', '='], '_', $v);
            if (! empty($v)) {
                $tmp_tags = $tmp_tags . ' ' . $k . '=' . $v;
            }
        }

        if ($measurement == 'ports') {
            foreach ($fields as $k => $v) {
                $measurement = $k;

                $this->putData('port.' . $measurement, $timestamp, $v, $tmp_tags);
            }
        } else {
            foreach ($fields as $k => $v) {
                $tmp_tags_key = $tmp_tags . ' ' . 'key' . '=' . $k;
                $this->putData($measurement, $timestamp, $v, $tmp_tags_key);
            }
        }
    }

    private function putData($measurement, $timestamp, $value, $tags)
    {
        try {
            $stat = Measurement::start('put');

            $line = sprintf('put net.%s %d %f %s', strtolower($measurement), $timestamp, $value, $tags);
            Log::debug("Sending to OpenTSDB: $line\n");
            $this->connection->write("$line\n"); // send $line into OpenTSDB

            $this->recordStatistic($stat->end());
        } catch (\Socket\Raw\Exception $e) {
            Log::error('OpenTSDB Error: ' . $e->getMessage());
        }
    }

    public static function isEnabled(): bool
    {
        return ObzoraConfig::get('opentsdb.enable', false);
    }
}
