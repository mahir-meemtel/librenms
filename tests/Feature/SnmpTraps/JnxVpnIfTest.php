<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class JnxVpnIfTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testVpnIfDown(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-VPN-MIB::jnxVpnIfDown
JUNIPER-VPN-MIB::jnxVpnIfVpnType.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex l2Circuit
JUNIPER-VPN-MIB::jnxVpnIfVpnName.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex \"$port->ifDescr\"
JUNIPER-VPN-MIB::jnxVpnIfIndex.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex $port->ifIndex
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX960",
            "l2Circuit on interface $port->ifDescr has gone down",
            'Could not handle JnxVpnIfDown trap',
            [Severity::Warning],
            $device,
        );
    }

    public function testVpnIfUp(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-VPN-MIB::jnxVpnIfUp
JUNIPER-VPN-MIB::jnxVpnIfVpnType.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex l2Circuit
JUNIPER-VPN-MIB::jnxVpnIfVpnName.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex $port->ifDescr
JUNIPER-VPN-MIB::jnxVpnIfIndex.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex $port->ifIndex
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX960",
            "l2Circuit on interface $port->ifDescr is now connected",
            'Could not handle JnxVpnIfUp trap',
            [Severity::Ok],
            $device,
        );
    }
}
