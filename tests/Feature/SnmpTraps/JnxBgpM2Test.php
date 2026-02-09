<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\BgpPeer;
use App\Models\Device;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class JnxBgpM2Test extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testBgpPeerUnknown(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $error = 'Unknown bgp peer handling bgpEstablished trap: 2001:d88:1::2';
        \Log::shouldReceive('error')->once()->with($error);

        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 BGP4-V2-MIB-JUNIPER::jnxBgpM2BackwardTransition
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLocalAddrType.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 ipv6
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLocalAddr.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "20 01 0D 88 00 01 00 00 00 00 00 00 00 00 00 01 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddrType.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 ipv6
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddr.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "20 01 0D 88 00 01 00 00 00 00 00 00 00 00 00 02 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLastErrorReceived.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "00 00 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerState.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 idle
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240
TRAP,
            [], // no log entries should be sent
            'Could not handle JnxBgpM2BackwardsTransition trap',
            device: $device,
        );
    }

    public function testBgpBackwardTransition(): void
    {
        $device = Device::factory()->create();
        /** @var Device $device */
        $bgppeer = BgpPeer::factory()->make(['bgpPeerIdentifier' => '2001:d88:1::2', 'bgpPeerState' => 'established']);
        /** @var BgpPeer $bgppeer */
        $device->bgppeers()->save($bgppeer);

        $error = 'Unknown bgp peer handling bgpEstablished trap: 2001:d88:1::2';
        \Log::shouldReceive('error')->never()->with($error);

        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 BGP4-V2-MIB-JUNIPER::jnxBgpM2BackwardTransition
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLocalAddrType.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 ipv6
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLocalAddr.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "20 01 0D 88 00 01 00 00 00 00 00 00 00 00 00 01 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddrType.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 ipv6
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddr.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "20 01 0D 88 00 01 00 00 00 00 00 00 00 00 00 02 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLastErrorReceived.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "00 00 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerState.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 idle
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240
TRAP,
            'BGP Peer 2001:d88:1::2 is now in the idle state',
            'Could not handle JnxBgpM2BackwardsTransition trap',
            [Severity::Error],
            $device,
        );
    }

    public function testBgpEstablished(): void
    {
        $device = Device::factory()->create();
        /** @var Device $device */
        $bgppeer = BgpPeer::factory()->make(['bgpPeerIdentifier' => '2001:d88:1::2', 'bgpPeerState' => 'idle']);
        /** @var BgpPeer $bgppeer */
        $device->bgppeers()->save($bgppeer);

        $error = 'Unknown bgp peer handling bgpEstablished trap: 2001:d88:1::2';
        \Log::shouldReceive('error')->never()->with($error);

        $this->assertTrapLogsMessage('{{ hostname }}
UDP: [{{ ip }}]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 BGP4-V2-MIB-JUNIPER::jnxBgpM2Established
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLocalAddrType.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 ipv6
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLocalAddr.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "20 01 0D 88 00 01 00 00 00 00 00 00 00 00 00 01 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddrType.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 ipv6
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddr.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "20 01 0D 88 00 01 00 00 00 00 00 00 00 00 00 02 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerLastErrorReceived.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 "00 00 "
BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerState.0.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.1.2.32.1.13.136.0.1.0.0.0.0.0.0.0.0.0.2 established
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240',
            'BGP Peer 2001:d88:1::2 is now in the established state',
            'Could not handle JnxBgpM2Established trap',
            [Severity::Ok],
            $device,
        );
    }
}
