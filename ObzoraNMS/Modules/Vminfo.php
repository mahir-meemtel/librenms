<?php
namespace ObzoraNMS\Modules;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use App\Observers\ModuleModelObserver;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\VminfoDiscovery;
use ObzoraNMS\Interfaces\Polling\VminfoPolling;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;

class Vminfo implements \ObzoraNMS\Interfaces\Module
{
    use SyncsModels;

    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return [];
    }

    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        // libvirt does not use snmp, only ssh tunnels
        return $status->isEnabledAndDeviceUp($os->getDevice(), check_snmp: ! ObzoraConfig::get('enable_libvirt')) && $os instanceof VminfoDiscovery;
    }

    /**
     * @inheritDoc
     */
    public function discover(OS $os): void
    {
        if ($os instanceof VminfoDiscovery) {
            $vms = $os->discoverVminfo();

            ModuleModelObserver::observe(\App\Models\Vminfo::class);
            $this->syncModels($os->getDevice(), 'vminfo', $vms);
        }
    }

    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice()) && $os instanceof VminfoPolling;
    }

    /**
     * @inheritDoc
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        if ($os->getDevice()->vminfo->isEmpty()) {
            return;
        }

        if ($os instanceof VminfoPolling) {
            $vms = $os->pollVminfo($os->getDevice()->vminfo);

            ModuleModelObserver::observe(\App\Models\Vminfo::class);
            $this->syncModels($os->getDevice(), 'vminfo', $vms);

            return;
        }

        // just run discovery again
        $this->discover($os);
    }

    public function dataExists(Device $device): bool
    {
        return $device->vminfo()->exists();
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return $device->vminfo()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        return [
            'vminfo' => $device->vminfo()->orderBy('vmwVmVMID')
                ->get()->map->makeHidden(['id', 'device_id']),
        ];
    }
}
