<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class JnxLdpSesTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testJnxLdpSesDownTrap(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $warning = "Snmptrap LdpSesDown: Could not find port at ifIndex $port->ifIndex for device: $device->hostname";
        \Log::shouldReceive('warning')->never()->with($warning);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-LDP-MIB::jnxLdpSesDown
JUNIPER-MPLS-LDP-MIB::jnxMplsLdpSesState.'.q.j..'.1.'.q.p..' nonexistent
JUNIPER-LDP-MIB::jnxLdpSesDownReason.0 allAdjacenciesDown
JUNIPER-LDP-MIB::jnxLdpSesDownIf.0 $port->ifIndex
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX480",
            "LDP session on interface $port->ifDescr is nonexistent due to allAdjacenciesDown",
            'Could not handle JnxLdpSesDown trap',
            [Severity::Warning],
            $device,
        );
    }

    public function testJnxLdpSesUpTrap(): void
    {
        $device = Device::factory()->create();
        /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']);
        /** @var Port $port */
        $device->ports()->save($port);

        $warning = "Snmptrap LdpSesUp: Could not find port at ifIndex $port->ifIndex for device: $device->hostname";
        \Log::shouldReceive('warning')->never()->with($warning);

        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-LDP-MIB::jnxLdpSesUp
JUNIPER-MPLS-LDP-MIB::jnxMplsLdpSesState.'.q.d..'.1.'.q.p..' operational
JUNIPER-LDP-MIB::jnxLdpSesUpIf.0 $port->ifIndex
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX960",
            "LDP session on interface $port->ifDescr is operational",
            'Could not handle JnxLdpSesUp trap',
            [Severity::Ok],
            $device,
        );
    }
}
