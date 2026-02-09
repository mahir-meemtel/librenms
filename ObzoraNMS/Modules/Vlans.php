<?php
namespace ObzoraNMS\Modules;

use App\Models\Device;
use App\Models\PortVlan;
use App\Models\Vlan;
use App\Observers\ModuleModelObserver;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;

class Vlans implements Module
{
    use SyncsModels;

    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return ['ports'];
    }

    /**
     * @inheritDoc
     */
    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    /**
     * @inheritDoc
     */
    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        if (defined('PHPUNIT_RUNNING')) {
            return false; // FIXME improve test suite to skip polling when polling data is null
        }

        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    /**
     * @inheritDoc
     */
    public function discover(OS $os): void
    {
        $vlans = $os->discoverVlans()->filter(function (?Vlan $data) {
            return ! empty($data->vlan_vlan);
        })->each(function (Vlan $data) {
            if (empty($data->vlan_name)) {
                $data->vlan_name = 'VLAN ' . $data->vlan_vlan; // default VLAN name
            }
        });

        ModuleModelObserver::observe(Vlan::class, 'VLANs');
        $vlans = $this->syncModels($os->getDevice(), 'vlans', $vlans);
        ModuleModelObserver::done();

        $ports = $os->discoverVlanPorts($vlans)->filter(function (PortVlan $data) {
            return ! empty($data->vlan) && ! empty($data->port_id);
        })->each(function (PortVlan $data) {
            $data->priority ??= 0;
            $data->state ??= 'unknown';
            $data->cost ??= 0;
        });

        ModuleModelObserver::observe(PortVlan::class, 'VLAN Ports');
        $this->syncModels($os->getDevice(), 'portsVlan', $ports);
        ModuleModelObserver::done();
    }

    /**
     * @inheritDoc
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        $this->discover($os);
    }

    /**
     * @inheritDoc
     */
    public function dataExists(Device $device): bool
    {
        return $device->vlans()->exists() || $device->portsVlan()->exists();
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return $device->vlans()->delete() + $device->portsVlan()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        // skip testing the poller as is the same as discovery.
        if ($type == 'poller') {
            return null;
        }

        return [
            'vlans' => $device->vlans()->orderBy('vlan_vlan')
                ->get()->map->makeHidden(['device_id', 'vlan_id']),
            'ports_vlans' => $device->portsVlan()
                ->orderBy('vlan')->orderBy('baseport')
                ->get()->map->makeHidden(['port_vlan_id', 'created_at', 'updated_at', 'device_id', 'port_id']),
        ];
    }
}
