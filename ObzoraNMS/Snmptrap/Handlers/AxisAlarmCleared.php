<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AxisAlarmCleared implements SnmptrapHandler
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
        $AlarmString = $trap->getOidData($trap->findOid('AXIS-VIDEO-MIB::alarmID'));
        // Handle data type errors in translated trap
        $AlarmID = preg_match('/^(?P<error>.+?)?(\:\s)?(?P<value>\d+)$/m', $AlarmString, $matches);
        if (! empty($matches['value'])) {
            $AlarmID = $matches['value'];
        } else {
            $AlarmID = $trap->getOidData($trap->findOid('AXIS-VIDEO-MIB::alarmID'));
        }

        $AlarmName = $trap->getOidData($trap->findOid('AXIS-VIDEO-MIB::alarmName'));
        $Message = $trap->getOidData($trap->findOid('AXIS-VIDEO-MIB::alarmText'));

        $trap->log("Axis Alarm Cleared Trap: Alarm ID $AlarmID for $AlarmName with text \"$Message\" has cleared", Severity::Ok);
    }
}
