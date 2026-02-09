<?php
namespace ObzoraNMS\Tests\Unit\Data;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('datastores')]
class DatastoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ObzoraConfig::forget([
            'graphite',
            'influxdb',
            'influxdbv2',
            'kafka',
            'opentsdb',
            'prometheus',
            'rrd',
        ]);
    }

    public function testDefaultInitialization(): void
    {
        $ds = $this->app->make('Datastore');
        $stores = $ds->getStores();
        $this->assertCount(1, $stores, 'Incorrect number of default stores enabled');

        $this->assertEquals('ObzoraNMS\Data\Store\Rrd', get_class($stores[0]), 'The default enabled store should be Rrd');
    }

    public function testInitialization(): void
    {
        ObzoraConfig::set('rrd.enable', false);
        ObzoraConfig::set('graphite.enable', true);
        ObzoraConfig::set('influxdb.enable', true);
        ObzoraConfig::set('influxdbv2.enable', true);
        ObzoraConfig::set('opentsdb.enable', true);
        ObzoraConfig::set('prometheus.enable', true);
        ObzoraConfig::set('kafka.enable', false);

        $ds = $this->app->make('Datastore');
        $stores = $ds->getStores();
        $this->assertCount(5, $stores, 'Incorrect number of default stores enabled');

        $enabled = array_map('get_class', $stores);

        $expected_enabled = [
            'ObzoraNMS\Data\Store\Graphite',
            'ObzoraNMS\Data\Store\InfluxDB',
            'ObzoraNMS\Data\Store\InfluxDBv2',
            'ObzoraNMS\Data\Store\OpenTSDB',
            'ObzoraNMS\Data\Store\Prometheus',
        ];

        $this->assertEquals($expected_enabled, $enabled, 'Expected all non-default stores to be initialized');
    }
}
