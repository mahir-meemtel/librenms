<?php
namespace ObzoraNMS\Data\Store;

use App\Facades\DeviceCache;
use App\Models\Device;
use App\Polling\Measure\Measurement;
use App\Polling\Measure\MeasurementCollection;
use ObzoraNMS\Interfaces\Data\Datastore as DatastoreContract;

abstract class BaseDatastore implements DatastoreContract
{
    private MeasurementCollection $stats;

    public function __construct()
    {
        $this->stats = new MeasurementCollection();
    }

    public function getStats(): MeasurementCollection
    {
        return $this->stats;
    }

    /**
     * Record statistics for operation
     *
     * @param  Measurement  $stat
     */
    protected function recordStatistic(Measurement $stat): void
    {
        $this->stats->record($stat);
    }

    protected function getDevice(array $meta): Device
    {
        if (isset($meta['device']) && $meta['device'] instanceof Device) {
            return $meta['device'];
        }

        return DeviceCache::getPrimary();
    }

    public function __destruct()
    {
        $this->terminate();
    }

    public function terminate(): void
    {
        // do nothing
    }
}
