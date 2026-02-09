<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class JnxDomAlarmTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testJnxDomAlarmSetTrap(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(); /** @var Port $port */
        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-DOM-MIB::jnxDomAlarmSet
IF-MIB::ifDescr.$port->ifIndex $port->ifDescr
JUNIPER-DOM-MIB::jnxDomLastAlarms.$port->ifIndex \"00 00 00 \"
JUNIPER-DOM-MIB::jnxDomCurrentAlarms.$port->ifIndex \"80 00 00 \"
JUNIPER-DOM-MIB::jnxDomCurrentAlarmDate.$port->ifIndex 2019-4-17,0:4:51.0,-5:0
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX480",
            "DOM alarm set for interface $port->ifDescr. Current alarm(s): input loss of signal",
            'Could not handle JnxDomAlarmSet',
            [Severity::Error],
            $device,
        );
    }

    public function testJnxDomAlarmClearTrap(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(); /** @var Port $port */
        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-DOM-MIB::jnxDomAlarmCleared
IF-MIB::ifDescr.$port->ifIndex $port->ifDescr
JUNIPER-DOM-MIB::jnxDomLastAlarms.$port->ifIndex \"00 00 00 \"
JUNIPER-DOM-MIB::jnxDomCurrentAlarms.$port->ifIndex \"E8 01 00 \"
JUNIPER-DOM-MIB::jnxDomCurrentAlarmDate.$port->ifIndex 2019-4-17,0:4:51.0,-5:0
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX480",
            "DOM alarm cleared for interface $port->ifDescr. Cleared alarm(s): input loss of signal, input loss of lock, input rx path not ready, input laser power low, module not ready",
            'Could not handle JnxDomAlarmCleared',
            [Severity::Ok],
            $device,
        );
    }
}
