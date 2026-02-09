<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class CiscoErrDisableInterfaceEventTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    /**
     * Test CiscoErrDisableInterfaceEvent trap handle
     *
     * @return void
     */
    public function testErrDisableInterfaceEvent(): void
    {
        $device = Device::factory()->create();
        /** @var Device $device */
        $port = Port::factory()->make();
        /** @var Port $port */
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 18:30:30.32
SNMPv2-MIB::snmpTrapOID.0 CISCO-ERR-DISABLE-MIB::cErrDisableInterfaceEventRev1
CISCO-ERR-DISABLE-MIB::cErrDisableIfStatusCause.$port->ifIndex.0 bpduGuard",
            'SNMP TRAP: bpduGuard error detected on ' . $port->ifName . ' (Description: ' . $port->ifDescr . '). ' . $port->ifName . ' in err-disable state.',
            'Could not handle testErrDisableInterfaceEvent trap',
            [Severity::Warning],
            $device,
        );
    }

    /**
     * Test CiscoErrDisableBadIfIndex trap handle
     *
     * @return void
     */
    public function testErrDisableBadIfIndex(): void
    {
        $device = Device::factory()->create();
        /** @var Device $device */
        $port = Port::factory()->make(['ifIndex' => 1]);
        /** @var Port $port */
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 18:30:30.32
SNMPv2-MIB::snmpTrapOID.0 CISCO-ERR-DISABLE-MIB::cErrDisableInterfaceEventRev1
CISCO-ERR-DISABLE-MIB::cErrDisableIfStatusCause.10.0 bpduGuard",
            'SNMP TRAP: bpduGuard error detected on unknown port. Either ifIndex is not found in the trap, or it does not match a port on this device.',
            'Could not handle testErrDisableBadIfIndex trap',
            [Severity::Warning],
            $device,
        );
    }
}
