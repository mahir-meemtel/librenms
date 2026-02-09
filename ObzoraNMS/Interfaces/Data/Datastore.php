<?php
namespace ObzoraNMS\Interfaces\Data;

use App\Polling\Measure\MeasurementCollection;

interface Datastore extends WriteInterface
{
    /**
     * Check if this is enabled by the configuration
     *
     * @return bool
     */
    public static function isEnabled(): bool;

    /**
     * The name of this datastore
     *
     * @return string
     */
    public function getName(): string;

    public function getStats(): MeasurementCollection;

    public function terminate(): void;
}
