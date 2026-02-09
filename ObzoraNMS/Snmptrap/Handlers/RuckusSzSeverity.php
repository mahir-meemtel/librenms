<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Enum\Severity;

class RuckusSzSeverity
{
    public static function getSeverity(string $severity): Severity
    {
        return match ($severity) {
            'Critical' => Severity::Error,
            'Major', 'Minor' => Severity::Warning,
            'Warning' => Severity::Notice,
            default => Severity::Info,
        };
    }
}
