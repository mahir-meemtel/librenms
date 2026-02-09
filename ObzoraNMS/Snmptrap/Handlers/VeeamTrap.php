<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Enum\Severity;

abstract class VeeamTrap
{
    protected function getResultSeverity(string $result): Severity
    {
        return match ($result) {
            'Success' => Severity::Ok,
            'Warning' => Severity::Warning,
            'Failed' => Severity::Error,
            default => Severity::Unknown,
        };
    }
}
