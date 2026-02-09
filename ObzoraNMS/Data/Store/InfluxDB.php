<?php
namespace ObzoraNMS\Data\Store;

use App\Facades\ObzoraConfig;
use App\Polling\Measure\Measurement;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Driver\UDP;
use Log;

class InfluxDB extends BaseDatastore
{
    /** @var Database */
    private $connection;

    public function __construct(Database $influx)
    {
        parent::__construct();
        $this->connection = $influx;

        // if the database doesn't exist, create it.
        try {
            if (! $influx->exists()) {
                $influx->create();
            }
        } catch (\Exception $e) {
            Log::warning('InfluxDB: Could not create database');
        }
    }

    public function getName(): string
    {
        return 'InfluxDB';
    }

    public static function isEnabled(): bool
    {
        return ObzoraConfig::get('influxdb.enable', false);
    }

    /**
     * @inheritDoc
     */
    public function write(string $measurement, array $fields, array $tags = [], array $meta = []): void
    {
        $stat = Measurement::start('write');
        $tmp_fields = [];
        $tmp_tags['hostname'] = $this->getDevice($meta)->hostname;
        foreach ($tags as $k => $v) {
            if (empty($v)) {
                $v = '_blank_';
            }
            $tmp_tags[$k] = $v;
        }
        foreach ($fields as $k => $v) {
            if ($k == 'time') {
                $k = 'rtime';
            }

            if (($value = $this->forceType($v)) !== null) {
                $tmp_fields[$k] = $value;
            }
        }

        if (empty($tmp_fields)) {
            Log::warning('All fields empty, skipping update', ['orig_fields' => $fields]);

            return;
        }

        Log::debug('InfluxDB data: ', [
            'measurement' => $measurement,
            'tags' => $tmp_tags,
            'fields' => $tmp_fields,
        ]);

        try {
            $points = [
                new \InfluxDB\Point(
                    $measurement,
                    null, // the measurement value
                    $tmp_tags,
                    $tmp_fields // optional additional fields
                ),
            ];

            $this->connection->writePoints($points);
            $this->recordStatistic($stat->end());
        } catch (\InfluxDB\Exception $e) {
            Log::error('InfluxDB exception: ' . $e->getMessage());
            Log::debug($e->getTraceAsString());
        }
    }

    /**
     * Create a new client and select the database
     *
     * @return Database
     */
    public static function createFromConfig()
    {
        $host = ObzoraConfig::get('influxdb.host', 'localhost');
        $transport = ObzoraConfig::get('influxdb.transport', 'http');
        $port = ObzoraConfig::get('influxdb.port', 8086);
        $db = ObzoraConfig::get('influxdb.db', 'obzora');
        $username = ObzoraConfig::get('influxdb.username', '');
        $password = ObzoraConfig::get('influxdb.password', '');
        $timeout = ObzoraConfig::get('influxdb.timeout', 0);
        $verify_ssl = ObzoraConfig::get('influxdb.verifySSL', false);

        $client = new Client($host, $port, $username, $password, $transport == 'https', $verify_ssl, $timeout, $timeout);

        if ($transport == 'udp') {
            $client->setDriver(new UDP($host, $port));
        }

        // Suppress InfluxDB\Database::create(): Implicitly marking parameter $retentionPolicy as nullable is deprecated
        return @$client->selectDB($db);
    }

    private function forceType($data)
    {
        /*
         * It is not trivial to detect if something is a float or an integer, and
         * therefore may cause breakages on inserts.
         * Just setting every number to a float gets around this, but may introduce
         * inefficiencies.
         */

        if (is_numeric($data)) {
            return floatval($data);
        }

        return $data === 'U' ? null : $data;
    }
}
