<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxVpnIfUp implements SnmptrapHandler
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

        if (substr($vpnName, 0, 6) === 'vt/lsi') {
            $vpnDevice = substr($vpnName, 7, 15);
            $trap->log("$vpnType to device $vpnDevice is now connected", Severity::Ok);
        } else {
            $trap->log("$vpnType on interface $vpnName is now connected", Severity::Ok);
        }
    }
}
