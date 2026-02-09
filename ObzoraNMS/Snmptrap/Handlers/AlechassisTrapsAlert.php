<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AlechassisTrapsAlert implements SnmptrapHandler
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
        $descr_aos6 = $trap->getOidData($trap->findOid('ALCATEL-IND1-CHASSIS-MIB::chassisTrapsAlertDescr'));
        $descr_aos7 = $trap->getOidData($trap->findOid('ALCATEL-IND1-CHASSIS-MIB::chassisTrapsAlertDescr.0'));

        if (! empty($descr_aos6)) {
            $trap->log("$descr_aos6");
        } elseif (! empty($descr_aos7)) {
            $trap->log("$descr_aos7");
        }
    }
}
