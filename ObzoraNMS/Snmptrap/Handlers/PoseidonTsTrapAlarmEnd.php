<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class PoseidonTsTrapAlarmEnd implements SnmptrapHandler
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
        $oid = $trap->findOid('POSEIDON-MIB::tsAlarmId');
        $id = substr($oid, strlen($oid) + 1);
        $AlarmID = $trap->getOidData($trap->findOid('POSEIDON-MIB::tsAlarmId.' . $id));
        $AlarmDescr = $trap->getOidData($trap->findOid('POSEIDON-MIB::tsAlarmDescr.' . $id));

        $trap->log("Poseidon Alarm End: Alarm ID $AlarmID: $AlarmDescr. Check the following Poseidon Alarm State Change trap for details", Severity::Ok);
    }
}
