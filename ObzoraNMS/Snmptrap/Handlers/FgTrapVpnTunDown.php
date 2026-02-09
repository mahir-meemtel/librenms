<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class FgTrapVpnTunDown implements SnmptrapHandler
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
        $remoteGw = $trap->getOidData($trap->findOid('FORTIGATE-MIB::fgVpnTrapRemoteGateway'));
        $tunName = $trap->getOidData($trap->findOid('FORTIGATE-MIB::fgVpnTrapPhase1Name'));
        $trap->log("VPN tunnel $tunName to $remoteGw is down", Severity::Notice);
    }
}
