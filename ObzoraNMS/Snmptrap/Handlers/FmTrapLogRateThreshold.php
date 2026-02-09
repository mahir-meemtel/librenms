<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class FmTrapLogRateThreshold implements SnmptrapHandler
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
        $logRate = $trap->getOidData($trap->findOid('FORTINET-FORTIMANAGER-FORTIANALYZER-MIB::fmLogRate'));
        $logThresh = $trap->getOidData($trap->findOid('FORTINET-FORTIMANAGER-FORTIANALYZER-MIB::fmLogRateThreshold'));
        $trap->log("Recommended log rate exceeded. Current Rate: $logRate Recommended Rate: $logThresh", Severity::Notice);
    }
}
