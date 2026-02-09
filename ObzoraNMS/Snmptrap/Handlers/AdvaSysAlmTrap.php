<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AdvaSysAlmTrap implements SnmptrapHandler
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
        $alSeverity = $trap->getOidData($trap->findOid('CM-ALARM-MIB::cmSysAlmNotifCode'));
        $logSeverity = match ($alSeverity) {
            'critical' => Severity::Error,
            'major' => Severity::Warning,
            'minor' => Severity::Notice,
            'cleared' => Severity::Ok,
            default => Severity::Info,
        };

        $sysAlmDescr = $trap->getOidData($trap->findOid('CM-ALARM-MIB::cmSysAlmDescr'));
        $trap->log("System Alarm: $sysAlmDescr Status: $alSeverity", $logSeverity);
    }
}
