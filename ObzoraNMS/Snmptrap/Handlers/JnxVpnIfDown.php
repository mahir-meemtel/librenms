<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxVpnIfDown implements SnmptrapHandler
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
        $vpnType = $trap->getOidData($trap->findOid('JUNIPER-VPN-MIB::jnxVpnIfVpnType'));
        $vpnName = $trap->getOidData($trap->findOid('JUNIPER-VPN-MIB::jnxVpnIfVpnName'));

        $trap->log("$vpnType on interface $vpnName has gone down", Severity::Warning);
    }
}
