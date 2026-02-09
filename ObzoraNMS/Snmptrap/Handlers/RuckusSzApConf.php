<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusSzApConf implements SnmptrapHandler
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
        $location = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPLocation'));
        $configId = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZAPConfigID'));
        $severity = RuckusSzSeverity::getSeverity($trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity')));
        $trap->log("AP at location $location configuration updated with config-id $configId", $severity);
    }
}
