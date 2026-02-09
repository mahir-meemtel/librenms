<?php
namespace ObzoraNMS\Tests\Unit;

use App\Models\Device;
use App\Models\Ipv4Address;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Tests\DBTestCase;

class DeviceTest extends DBTestCase
{
    use DatabaseTransactions;

    public function testFindByHostname(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $found = Device::findByHostname($device->hostname);
        $this->assertNotNull($found);
        $this->assertEquals($device->device_id, $found->device_id, 'Did not find the correct device');
    }

    public function testFindByIpFail(): void
    {
        $found = Device::findByIp('this is not an ip');
        $this->assertNull($found);
    }

    public function testFindByIpv4Fail(): void
    {
        $found = Device::findByIp('182.43.219.43');
        $this->assertNull($found);
    }

    public function testFindByIpv6Fail(): void
    {
        $found = Device::findByIp('341a:234d:3429:9845:909f:fd32:1930:32dc');
        $this->assertNull($found);
    }

    public function testFindIpButNoPort(): void
    {
        $ipv4 = Ipv4Address::factory()->create(); /** @var Ipv4Address $ipv4 */
        Port::destroy($ipv4->port_id);

        $found = Device::findByIp($ipv4->ipv4_address);
        $this->assertNull($found);
    }

    public function testFindByIp(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $found = Device::findByIp($device->ip);
        $this->assertNotNull($found);
        $this->assertEquals($device->device_id, $found->device_id, 'Did not find the correct device');
    }

    public function testFindByIpHostname(): void
    {
        $ip = '192.168.234.32';
        $device = Device::factory()->create(['hostname' => $ip]); /** @var Device $device */
        $found = Device::findByIp($ip);
        $this->assertNotNull($found);
        $this->assertEquals($device->device_id, $found->device_id, 'Did not find the correct device');
    }

    public function testFindByIpThroughPort(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(); /** @var Port $port */
        $device->ports()->save($port);
        // test ipv4 lookup of device
        $ipv4 = Ipv4Address::factory()->make(); /** @var Ipv4Address $ipv4 */
        $port->ipv4()->save($ipv4);

        $found = Device::findByIp($ipv4->ipv4_address);
        $this->assertNotNull($found);
        $this->assertEquals($device->device_id, $found->device_id, 'Did not find the correct device');
    }
}
