<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use ObzoraNMS\Util\AutonomousSystem;
use Log;

class BgpBackwardTransition implements SnmptrapHandler
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
        $state_oid = $trap->findOid('BGP4-MIB::bgpPeerState');
        $bgpPeerIp = substr($state_oid, 23);

        $bgpPeer = $device->bgppeers()->where('bgpPeerIdentifier', $bgpPeerIp)->first();

        if (! $bgpPeer) {
            Log::error('Unknown bgp peer handling bgpBackwardTransition trap: ' . $bgpPeerIp);

            return;
        }

        $bgpPeer->bgpPeerState = $trap->getOidData($state_oid);

        if ($bgpPeer->isDirty('bgpPeerState')) {
            $trap->log('SNMP Trap: BGP Down ' . $bgpPeer->bgpPeerIdentifier . ' ' . AutonomousSystem::get($bgpPeer->bgpPeerRemoteAs)->name() . ' is now ' . $bgpPeer->bgpPeerState, severity: Severity::Error, type: 'bgpPeer',
                reference: $bgpPeerIp);
        }

        $bgpPeer->save();
    }
}
