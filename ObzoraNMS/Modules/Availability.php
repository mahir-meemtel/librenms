<?php
namespace ObzoraNMS\Modules;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\RRD\RrdDefinition;
use ObzoraNMS\Util\Time;

class Availability implements Module
{
    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return [];
    }

    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function discover(OS $os): void
    {
    }

    /**
     * @inheritDoc
     */
    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        $os->enableGraph('availability');

        $valid_ids = [];
        foreach (ObzoraConfig::get('graphing.availability') as $duration) {
            // update database with current calculation
            $avail = \App\Models\Availability::updateOrCreate([
                'device_id' => $os->getDeviceId(),
                'duration' => $duration,
            ], [
                'availability_perc' => \ObzoraNMS\Device\Availability::availability($os->getDevice(), $duration),
            ]);
            $valid_ids[] = $avail->availability_id;

            // update rrd
            $datastore->put($os->getDeviceArray(), 'availability', [
                'name' => $duration,
                'rrd_def' => RrdDefinition::make()->addDataset('availability', 'GAUGE', 0, 100),
                'rrd_name' => ['availability', $duration],
            ], [
                'availability' => $avail->availability_perc,
            ]);

            // output info
            $human_duration = Time::formatInterval($duration, parts: 1);
            \Log::info(str_pad($human_duration, 7) . ' : ' . $avail->availability_perc . '%');
        }

        // cleanup
        $os->getDevice()->availability()->whereNotIn('availability_id', $valid_ids)->delete();
    }

    public function dataExists(Device $device): bool
    {
        return $device->availability()->exists();
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return $device->availability()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        if ($type == 'discovery') {
            return null;
        }

        return [
            'availability' => $device->availability()->orderBy('duration')
                ->get()->map->makeHidden(['availability_id', 'device_id']),
        ];
    }
}
