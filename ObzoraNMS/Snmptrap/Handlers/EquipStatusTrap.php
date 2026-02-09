<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class EquipStatusTrap implements SnmptrapHandler
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
        $state = $trap->getOidData('EQUIPMENT-MIB::equipStatus.0');

        $severity = $this->getSeverity($state);
        $trap->log('SNMP Trap: Equipment Status  ' . $state, $severity, 'state');
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
