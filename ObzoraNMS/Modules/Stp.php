<?php
namespace ObzoraNMS\Modules;

use App\Models\Device;
use App\Models\PortStp;
use App\Observers\ModuleModelObserver;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;

class Stp implements Module
{
    use SyncsModels;

    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return ['ports', 'vlans'];
    }

    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    public function discover(OS $os): void
    {
        $device = $os->getDevice();

        $instances = $os->discoverStpInstances();
        ModuleModelObserver::observe(\App\Models\Stp::class, 'Instances');
        $this->syncModels($device, 'stpInstances', $instances);
        ModuleModelObserver::done();

        $ports = $os->discoverStpPorts($instances);
        ModuleModelObserver::observe(PortStp::class, 'Ports');
        $this->syncModels($device, 'stpPorts', $ports);
        ModuleModelObserver::done();
    }

    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        $device = $os->getDevice();

        $instances = $device->stpInstances;
        $instances = $os->pollStpInstances($instances);
        ModuleModelObserver::observe(\App\Models\Stp::class, 'Instances');
        $this->syncModels($device, 'stpInstances', $instances);
        ModuleModelObserver::done();

        $ports = $device->stpPorts;
        ModuleModelObserver::observe(PortStp::class, 'Ports');
        $this->syncModels($device, 'stpPorts', $ports);
        ModuleModelObserver::done();
    }

    public function dataExists(Device $device): bool
    {
        return $device->stpInstances()->exists() || $device->stpPorts()->exists();
    }

    public function cleanup(Device $device): int
    {
        $deleted = $device->stpInstances()->delete();
        $deleted += $device->stpPorts()->delete();

        return $deleted;
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        return [
            'stp' => $device->stpInstances()->orderBy('bridgeAddress')
                ->get()->map->makeHidden(['stp_id', 'device_id']),
            'ports_stp' => $device->portsStp()->orderBy('port_index')
                ->leftJoin('ports', 'ports_stp.port_id', 'ports.port_id')
                ->select(['ports_stp.*', 'ifIndex'])
                ->get()->map->makeHidden(['port_stp_id', 'device_id', 'port_id']),
        ];
    }
}
