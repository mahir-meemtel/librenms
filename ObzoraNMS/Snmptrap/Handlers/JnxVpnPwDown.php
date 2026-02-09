<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxVpnPwDown implements SnmptrapHandler
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
        $vpnType = $trap->getOidData($trap->findOid('JUNIPER-VPN-MIB::jnxVpnPwVpnType'));
        $vpnName = $trap->getOidData($trap->findOid('JUNIPER-VPN-MIB::jnxVpnPwVpnName'));

        $trap->log("$vpnType on a pseudowire belonging to $vpnName has gone down", Severity::Warning);
    }
}
