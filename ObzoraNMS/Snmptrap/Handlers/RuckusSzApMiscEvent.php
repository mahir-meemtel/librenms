<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusSzApMiscEvent implements SnmptrapHandler
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
        $severity = RuckusSzSeverity::getSeverity($trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity')));
        $eventDescr = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZEventDescription'));
        $trap->log("AP event: $eventDescr", $severity);
    }
}
