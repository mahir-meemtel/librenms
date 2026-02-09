<?php
namespace ObzoraNMS\Tests\Unit\Data;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use InfluxDB\Point;
use ObzoraNMS\Data\Store\InfluxDB;
use ObzoraNMS\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('datastores')]
class InfluxDBStoreTest extends TestCase
{
    public function testBadSettings(): void
    {
        ObzoraConfig::set('influxdb.host', '');
        ObzoraConfig::set('influxdb.port', 'abc');
        $influx = new InfluxDB(InfluxDB::createFromConfig());

        \Log::shouldReceive('debug');
        \Log::shouldReceive('error')->once()->with('InfluxDB exception: Unable to parse URI: http://:0'); // the important one
        $influx->write('fake', ['one' => 1]);
    }

    public function testSimpleWrite(): void
    {
        // Create a mock of the Random Interface
        $mock = \Mockery::mock(\InfluxDB\Database::class);

        $mock->shouldReceive('exists')->once()->andReturn(true);
        $influx = new InfluxDB($mock);

        $device = new Device(['hostname' => 'testhost']);
        $measurement = 'testmeasure';
        $tags = ['ifName' => 'testifname', 'type' => 'testtype'];
        $fields = ['ifIn' => 234234.0, 'ifOut' => 53453.0];
        $meta = ['device' => $device];

        $expected = [new Point($measurement, null, ['hostname' => $device->hostname] + $tags, $fields)];

        $mock->shouldReceive('writePoints')->withArgs([$expected])->once();
        $influx->write($measurement, $fields, $tags, $meta);
    }
}
