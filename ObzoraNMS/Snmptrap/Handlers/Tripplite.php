<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Snmptrap\Trap;

class Tripplite
{
    protected function getSeverity(Trap $trap): Severity
    {
        return match ($trap->getOidData('TRIPPLITE-PRODUCTS::tlpAlarmType')) {
            'critical' => Severity::Error,
            'warning' => Severity::Warning,
            'info' => Severity::Info,
            'status' => Severity::Notice,
            default => Severity::Warning,
        };
    }

    protected function describe(Trap $trap): string
    {
        return 'Trap Alarm ' . $trap->getOidData('TRIPPLITE-PRODUCTS::tlpAlarmState') . ': ' . $trap->getOidData('TRIPPLITE-PRODUCTS::tlpAlarmDetail');
    }
}
