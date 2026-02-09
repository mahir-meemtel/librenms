<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class LogTrap implements SnmptrapHandler
{
    /**
     * Handle snmptrap.
     * Data is pre-parsed and delivered as a Trap.
     *
     * @param  Device  $device
     * @param  Trap  $trap
     * @return void
     */
    public function handle(Device $device, Trap $trap): void
    {
        $index = $trap->findOid('LOG-MIB::logIndex');
        $index = $trap->getOidData($index);

        $logName = $trap->getOidData('LOG-MIB::logName.' . $index);
        $logEvent = $trap->getOidData('LOG-MIB::logEvent.' . $index);
        $logPC = $trap->getOidData('LOG-MIB::logPC.' . $index);
        $logAI = $trap->getOidData('LOG-MIB::logAI.' . $index);
        $state = $trap->getOidData('LOG-MIB::logEquipStatusV2.' . $index);

        $severity = $this->getSeverity($state);
        $trap->log('SNMP Trap: Log ' . $logName . ' ' . $logEvent . ' ' . $logPC . ' ' . $logAI . ' ' . $state, $severity, 'log');
    }

    private function getSeverity(string $state): Severity
    {
        return match ($state) {
            'warning', '3', 'major', '5' => Severity::Warning,
            'critical', '4' => Severity::Error,
            'minor', '2' => Severity::Notice,
            'nonAlarmed', '1' => Severity::Ok,
            default => Severity::Unknown,
        };
    }
}
