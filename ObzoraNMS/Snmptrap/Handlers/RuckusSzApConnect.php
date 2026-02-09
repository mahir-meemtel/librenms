<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusSzApConnect implements SnmptrapHandler
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
        $reason = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventReason'));
        $severity = RuckusSzSeverity::getSeverity($trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity')));
        $trap->log("AP at location $location has connected to the SmartZone with reason $reason", $severity);
    }
}
