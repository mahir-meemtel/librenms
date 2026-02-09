<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use ObzoraNMS\Util\IP;
use Log;

class JnxBgpM2BackwardTransition implements SnmptrapHandler
{
    /**
     * Handle snmptrap.
     * Data is pre-parsed and delivered as a Trap.
     *
     * @param  Device  $device
     * @param  Trap  $trap
     * @return void
     */
    public function handle(Device $device, Trap $trap)
    {
        $peerState = $trap->getOidData($trap->findOid('BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerState'));
        $peerAddr = IP::fromHexString($trap->getOidData($trap->findOid('BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAddr.')));

        $bgpPeer = $device->bgppeers()->where('bgpPeerIdentifier', $peerAddr)->first();

        if (! $bgpPeer) {
            Log::error('Unknown bgp peer handling bgpEstablished trap: ' . $peerAddr);

            return;
        }

        $bgpPeer->bgpPeerState = $peerState;

        if ($bgpPeer->isDirty('bgpPeerState')) {
            $trap->log("BGP Peer $peerAddr is now in the $peerState state", Severity::Error);
        }

        $bgpPeer->save();
    }
}
