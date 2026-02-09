<?php
namespace App\Models\Traits;

use ObzoraNMS\Enum\Severity;

trait HasThresholds
{
    public function currentStatus(): Severity
    {
        if ($this->sensor_class == 'state' && $this instanceof \App\Models\Sensor) {
            return $this->currentTranslation()?->severity() ?? Severity::Unknown;
        }

        if ($this->sensor_current === null) {
            return Severity::Unknown;
        }

        if ($this->sensor_limit !== null && $this->sensor_current >= $this->sensor_limit) {
            return Severity::Error;
        }
        if ($this->sensor_limit_low !== null && $this->sensor_current <= $this->sensor_limit_low) {
            return Severity::Error;
        }

        if ($this->sensor_limit_warn !== null && $this->sensor_current >= $this->sensor_limit_warn) {
            return Severity::Warning;
        }

        if ($this->sensor_limit_low_warn !== null && $this->sensor_current <= $this->sensor_limit_low_warn) {
            return Severity::Warning;
        }

        return Severity::Ok;
    }

    public function hasThresholds(): bool
    {
        return $this->sensor_limit_low !== null
            || $this->sensor_limit_low_warn !== null
            || $this->sensor_limit_warn !== null
            || $this->sensor_limit !== null;
    }

    public function doesntHaveThresholds(): bool
    {
        return ! $this->hasThresholds();
    }

    public function guessLimits(bool $high, bool $low): void
    {
        if ($high) {
            $this->sensor_limit = match ($this->sensor_class) {
                'temperature' => $this->sensor_current + 20,
                'voltage' => $this->sensor_current * 1.15,
                'humidity' => 70,
                'fanspeed' => $this->sensor_current * 1.80,
                'power_factor' => 1,
                'signal' => -30,
                'load' => 80,
                'airflow', 'snr', 'frequency', 'pressure', 'cooling' => $this->sensor_current * 1.05,
                default => null,
            };
        }

        if ($low) {
            $this->sensor_limit_low = match ($this->sensor_class) {
                'temperature' => $this->sensor_current - 10,
                'voltage' => $this->sensor_current * 0.85,
                'humidity' => 30,
                'fanspeed' => $this->sensor_current * 0.80,
                'power_factor' => -1,
                'signal' => -80,
                'airflow', 'snr', 'frequency', 'pressure', 'cooling' => $this->sensor_current * 0.95,
                default => null,
            };
        }
    }
}
