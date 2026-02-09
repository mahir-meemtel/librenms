<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class ApcPduNearOverload implements SnmptrapHandler
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
        //Get the PDU's name, affected phase, and the alarm string
        $pdu_id = ApcTrapUtil::getPduIdentName($trap);
        $phase_num = ApcTrapUtil::getPduPhaseNum($trap);
        $alarm_str = ApcTrapUtil::getApcTrapString($trap);
        $trap->log("$pdu_id phase $phase_num $alarm_str", Severity::Warning);
    }
}
