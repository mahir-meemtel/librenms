<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Mgnt2TrapNmsAlarm implements SnmptrapHandler
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
        $alarmObj = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogObjectClassIdentifier'));
        $sourcePm = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePm'));
        $slot = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogBoardNumber'));
        $portType = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortType'));
        $portNum = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortNumber'));
        $probCause = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogProbableCause'));
        $probSpecific = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSpecificProblem'));
        $probAdd = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAdditionalText'));
        $alarmSeverity = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSeverity'));

        // Adding additional info if it exists.
        if (! empty($probAdd)) {
            $probSpecific = "$probSpecific Additional info: $probAdd";
        }

        // Changing other to unknown
        if ($probCause == 'other') {
            $probCause = 'Unknown';
        }

        if ($alarmObj == 'port') {
            $msg = "Alarm on slot $slot, $sourcePm Port: $portType $portNum Issue: $probSpecific Possible Cause: $probCause";
        } else {
            $msg = "Alarm on slot $slot, $sourcePm Issue: $probSpecific Possible Cause: $probCause";
        }

        $severity = match ($alarmSeverity) {
            'cleared' => Severity::Ok,
            'critical', 'major' => Severity::Error,
            'minor', 'warning' => Severity::Warning,
            'indeterminate' => Severity::Unknown,
            default => Severity::Info,
        };

        $trap->log($msg, $severity);
    }
}
