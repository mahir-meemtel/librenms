<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxDomAlarmSet implements SnmptrapHandler
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
        $currentAlarm = $trap->getOidData($trap->findOid('JUNIPER-DOM-MIB::jnxDomCurrentAlarms'));
        $ifDescr = $trap->getOidData($trap->findOid('IF-MIB::ifDescr'));
        $alarmList = JnxDomAlarmId::getAlarms($currentAlarm);
        $trap->log("DOM alarm set for interface $ifDescr. Current alarm(s): $alarmList", Severity::Error);
    }
}
