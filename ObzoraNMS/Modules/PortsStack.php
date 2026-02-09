<?php
namespace ObzoraNMS\Modules;

use App\Facades\PortCache;
use App\Models\Device;
use App\Models\PortStack;
use App\Observers\ModuleModelObserver;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;

class PortsStack implements Module
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
        return false;
    }

    /**
     * @inheritDoc
     */
    public function discover(OS $os): void
    {
        $data = \SnmpQuery::enumStrings()->walk('IF-MIB::ifStackStatus');

        if (! $data->isValid()) {
            return;
        }

        $portStacks = $data->mapTable(function ($data, $lowIfIndex, $highIfIndex = null) use ($os) {
            if ($highIfIndex === null) {
                Log::debug('Skipping ' . $lowIfIndex . ' due to bad table index from the device');

                return null;
            }

            if ($lowIfIndex == '0' || $highIfIndex == '0') {
                return null;  // we don't care about the default entries for ports that have stacking enabled
            }

            return new PortStack([
                'high_ifIndex' => $highIfIndex,
                'high_port_id' => PortCache::getIdFromIfIndex($highIfIndex, $os->getDevice()),
                'low_ifIndex' => $lowIfIndex,
                'low_port_id' => PortCache::getIdFromIfIndex($lowIfIndex, $os->getDevice()),
                'ifStackStatus' => $data['IF-MIB::ifStackStatus'],
            ]);
        });

        ModuleModelObserver::observe(PortStack::class);
        $this->syncModels($os->getDevice(), 'portsStack', $portStacks->filter());
    }

    /**
     * @inheritDoc
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        // no polling
    }

    public function dataExists(Device $device): bool
    {
        return $device->portsStack()->exists();
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return $device->portsStack()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        if ($type == 'poller') {
            return null;
        }

        return [
            'ports_stack' => $device->portsStack()
                ->orderBy('high_ifIndex')->orderBy('low_ifIndex')
                ->get(['high_ifIndex', 'low_ifIndex', 'ifStackStatus']),
        ];
    }
}
