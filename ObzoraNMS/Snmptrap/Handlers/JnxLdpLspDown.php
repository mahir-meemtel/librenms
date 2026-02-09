<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxLdpLspDown implements SnmptrapHandler
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
        $lspForward = $trap->getOidData($trap->findOid('JUNIPER-LDP-MIB::jnxLdpLspFec'));
        $routerID = $trap->getOidData($trap->findOid('JUNIPER-LDP-MIB::jnxLdpRtrid'));
        $reason = $trap->getOidData($trap->findOid('JUNIPER-LDP-MIB::jnxLdpLspDownReason'));
        $instanceName = $trap->getOidData($trap->findOid('JUNIPER-LDP-MIB::jnxLdpInstanceName'));

        $trap->log("LDP session $instanceName from $routerID to $lspForward has gone down due to $reason", Severity::Warning);
    }
}
