<?php
namespace ObzoraNMS\Modules;

use App\Models\Device;
use App\Models\Transceiver;
use App\Observers\ModuleModelObserver;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\TransceiverDiscovery;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;

class Transceivers implements Module
{
    use SyncsModels;

    public function dependencies(): array
    {
        return ['ports'];
    }

    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice()) && $os instanceof TransceiverDiscovery;
    }

    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return false;
    }

    public function discover(OS $os): void
    {
        if ($os instanceof TransceiverDiscovery) {
            $discoveredTransceivers = $os->discoverTransceivers();

            // save transceivers
            ModuleModelObserver::observe(Transceiver::class);
            $this->syncModels($os->getDevice(), 'transceivers', $discoveredTransceivers);
        }
    }

    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        // no polling
    }

    public function dataExists(Device $device): bool
    {
        return $device->transceivers()->exists();
    }

    public function cleanup(Device $device): int
    {
        return $device->transceivers()->delete();
    }

    public function dump(Device $device, string $type): ?array
    {
        if ($type == 'poller') {
            return null;
        }

        return [
            'transceivers' => $device->transceivers()->orderBy('index')
                ->leftJoin('ports', 'transceivers.port_id', 'ports.port_id')
                ->select(['transceivers.*', 'ifIndex'])
                    ->get()->map->makeHidden(['id', 'created_at', 'updated_at', 'device_id', 'port_id']),
        ];
    }
}
