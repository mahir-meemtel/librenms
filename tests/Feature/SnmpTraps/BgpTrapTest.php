<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Facades\ObzoraConfig;
use App\Models\BgpPeer;
use App\Models\Device;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;
use ObzoraNMS\Util\AutonomousSystem;

class BgpTrapTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testBgpUp(): void
    {
        // Cache it to avoid DNS Lookup
        ObzoraConfig::set('astext.1', 'PHPUnit ASTEXT');
        $device = Device::factory()->create();
        /** @var Device $device */
        $bgppeer = BgpPeer::factory()->make(['bgpPeerState' => 'idle', 'bgpPeerRemoteAs' => 1]);
        /** @var BgpPeer $bgppeer */
        $device->bgppeers()->save($bgppeer);

        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 302:12:56:24.81
SNMPv2-MIB::snmpTrapOID.0 BGP4-MIB::bgpEstablished
BGP4-MIB::bgpPeerLastError.$bgppeer->bgpPeerIdentifier \"04 00 \"
BGP4-MIB::bgpPeerState.$bgppeer->bgpPeerIdentifier established\n",
            "SNMP Trap: BGP Up $bgppeer->bgpPeerIdentifier " . AutonomousSystem::get($bgppeer->bgpPeerRemoteAs)->name() . ' is now established',
            'Could not handle bgpEstablished',
            [Severity::Ok, 'bgpPeer', $bgppeer->bgpPeerIdentifier],
            $device,
        );

        $bgppeer = $bgppeer->fresh(); // refresh from database
        $this->assertEquals($bgppeer->bgpPeerState, 'established');
    }

    public function testBgpDown(): void
    {
        // Cache it to avoid DNS Lookup
        ObzoraConfig::set('astext.1', 'PHPUnit ASTEXT');
        $device = Device::factory()->create();
        /** @var Device $device */
        $bgppeer = BgpPeer::factory()->make(['bgpPeerState' => 'established', 'bgpPeerRemoteAs' => 1]);
        /** @var BgpPeer $bgppeer */
        $device->bgppeers()->save($bgppeer);

        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:57602->[185.29.68.52]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 302:12:55:33.47
SNMPv2-MIB::snmpTrapOID.0 BGP4-MIB::bgpBackwardTransition
BGP4-MIB::bgpPeerLastError.$bgppeer->bgpPeerIdentifier \"04 00 \"
BGP4-MIB::bgpPeerState.$bgppeer->bgpPeerIdentifier idle\n",
            "SNMP Trap: BGP Down $bgppeer->bgpPeerIdentifier " . AutonomousSystem::get($bgppeer->bgpPeerRemoteAs)->name() . ' is now idle',
            'Could not handle bgpBackwardTransition',
            [Severity::Error, 'bgpPeer', $bgppeer->bgpPeerIdentifier],
            $device,
        );

        $bgppeer = $bgppeer->fresh(); // refresh from database
        $this->assertEquals($bgppeer->bgpPeerState, 'idle');
    }
}
