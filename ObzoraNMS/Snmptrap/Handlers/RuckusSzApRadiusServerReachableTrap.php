<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusSzApRadiusServerReachableTrap implements SnmptrapHandler
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
        $apName = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPName'));
        $apIpv4 = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPIP'));
        $apIpv6 = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPIPv6'));
        $radiusIp = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZRadSrvrIp'));

        $message = "AP $apName ($apIpv4) is able to reach radius server $radiusIp";
        if (! empty($apIpv6)) {
            $message = "AP $apName ($apIpv4, $apIpv6) is able to reach radius server $radiusIp";
        }

        $trap->log("$message", Severity::Ok);
    }
}
