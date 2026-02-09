<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class JnxVpnPwTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testVpnPwDown(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-VPN-MIB::jnxVpnPwDown
JUNIPER-VPN-MIB::jnxVpnPwVpnType.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex l2Circuit
JUNIPER-VPN-MIB::jnxVpnPwVpnName.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex $port->ifDescr
JUNIPER-VPN-MIB::jnxVpnPwIndex.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex $port->ifIndex
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX480",
            "l2Circuit on a pseudowire belonging to $port->ifDescr has gone down",
            'Could not handle JnxVpnPwDown trap',
            [Severity::Warning],
            $device,
        );
    }

    public function testVpnPwUp(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-VPN-MIB::jnxVpnPwUp
JUNIPER-VPN-MIB::jnxVpnPwVpnType.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex l2Circuit
JUNIPER-VPN-MIB::jnxVpnPwVpnName.l2Circuit.\"ge-0/0/2.0\".$port->ifIndex $port->ifDescr
JUNIPER-VPN-MIB::jnxVpnPwIndex.l2Circuit.\"ge-0/02.0\".$port->ifIndex $port->ifIndex
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX960",
            "l2Circuit on a pseudowire belonging to $port->ifDescr is now connected",
            'Could not handle JnxVpnPwUp trap',
            [Severity::Ok],
            $device,
        );
    }
}
