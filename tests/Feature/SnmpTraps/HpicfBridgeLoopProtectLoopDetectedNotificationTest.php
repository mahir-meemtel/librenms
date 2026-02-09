<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class HpicfBridgeLoopProtectLoopDetectedNotificationTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    /**
     * Test HpicfBridgeLoopProtectLoopDetectedNotification.php handler
     *
     * @return void
     */
    public function testHpicfBridgeLoopProtectLoopDetectedNotification(): void
    {
        $device = Device::factory()->create();
        $port = Port::factory()->make(['ifIndex' => '1', 'ifDescr' => 'A1']);
        $device->ports()->save($port);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:44289->[1.1.1.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 82:19:24:56.09
SNMPv2-MIB::snmpTrapOID.0 HP-ICF-BRIDGE::hpicfBridgeLoopProtectLoopDetectedNotification
IF-MIB::ifIndex.$port->ifIndex $port->ifIndex
HP-ICF-BRIDGE::hpicfBridgeLoopProtectPortLoopCount 1
HP-ICF-BRIDGE::hpicfBridgeLoopProtectPortReceiverAction disableTx",
            "Loop Detected $port->ifDescr (Count 1, Action disableTx)",
            'Could not handle HP-ICF-BRIDGE::HpicfBridgeLoopProtectLoopDetectedNotification trap',
            [Severity::Warning, 'loop', $port->ifDescr],
            $device
        );
    }
}
