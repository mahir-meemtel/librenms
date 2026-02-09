<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Ipv4Address;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class JnxLdpLspTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testLdpLspDownTrap(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ipv4 = Ipv4Address::factory()->make(); /** @var Ipv4Address $ipv4 */
        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-LDP-MIB::jnxLdpLspDown
JUNIPER-LDP-MIB::jnxLdpLspFec.0 $ipv4->ipv4_address
JUNIPER-LDP-MIB::jnxLdpRtrid.0 $device->ip
JUNIPER-LDP-MIB::jnxLdpLspDownReason.0 topologyChanged
JUNIPER-LDP-MIB::jnxLdpLspFecLen.0 32
JUNIPER-LDP-MIB::jnxLdpInstanceName.0 \"test instance down\"
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX480",
            "LDP session test instance down from $device->ip to $ipv4->ipv4_address has gone down due to topologyChanged",
            'Could not handle JnxLdpLspDown trap',
            [Severity::Warning],
            $device,
        );
    }

    public function testLdpLspUpTrap(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ipv4 = Ipv4Address::factory()->make(); /** @var Ipv4Address $ipv4 */
        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-LDP-MIB::jnxLdpLspUp
JUNIPER-LDP-MIB::jnxLdpLspFec.0 $ipv4->ipv4_address
JUNIPER-LDP-MIB::jnxLdpRtrid.0 $device->ip
JUNIPER-LDP-MIB::jnxLdpLspFecLen.0 32
JUNIPER-LDP-MIB::jnxLdpInstanceName.0 \"test instance up\"
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX480",
            "LDP session test instance up from $device->ip to $ipv4->ipv4_address is now up.",
            'Could not handle JnxLdpLspUp trap',
            [Severity::Ok],
            $device,
        );
    }
}
