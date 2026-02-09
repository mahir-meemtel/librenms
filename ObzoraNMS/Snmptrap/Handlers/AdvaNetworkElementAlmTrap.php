<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AdvaNetworkElementAlmTrap implements SnmptrapHandler
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
        $alSeverity = $trap->getOidData($trap->findOid('CM-ALARM-MIB::cmNetworkElementAlmNotifCode'));
        $logSeverity = match ($alSeverity) {
            'critical' => Severity::Error,
            'major' => Severity::Warning,
            'minor' => Severity::Notice,
            'cleared' => Severity::Ok,
            default => Severity::Info,
        };

        $almDescr = $trap->getOidData($trap->findOid('CM-ALARM-MIB::cmNetworkElementAlmDescr'));
        $almObjName = $trap->getOidData($trap->findOid('CM-ALARM-MIB::cmNetworkElementAlmObjectName'));
        $trap->log("Alarming Element: $almObjName Description: $almDescr Severity: $alSeverity", $logSeverity);
    }
}
