<?php
namespace ObzoraNMS\Modules;

use App\Models\Device;
use App\Models\Sla;
use App\Observers\ModuleModelObserver;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\SlaDiscovery;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\Interfaces\Polling\SlaPolling;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\RRD\RrdDefinition;

class Slas implements Module
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
        return $status->isEnabledAndDeviceUp($os->getDevice()) && $os instanceof SlaDiscovery;
    }

    /**
     * Discover this module. Heavier processes can be run here
     * Run infrequently (default 4 times a day)
     *
     * @param  OS  $os
     */
    public function discover(OS $os): void
    {
        if ($os instanceof SlaDiscovery) {
            $slas = $os->discoverSlas();
            ModuleModelObserver::observe(Sla::class);
            $this->syncModels($os->getDevice(), 'slas', $slas);
        }
    }

    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice()) && $os instanceof SlaPolling;
    }

    /**
     * Poll data for this module and update the DB / RRD.
     * Try to keep this efficient and only run if discovery has indicated there is a reason to run.
     * Run frequently (default every 5 minutes)
     *
     * @param  OS  $os
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        if ($os instanceof SlaPolling) {
            // Gather our SLA's from the DB.
            $slas = $os->getDevice()->slas()
                ->where('deleted', 0)->get();

            if ($slas->isNotEmpty()) {
                // We have SLA's, lets go!!!
                $os->pollSlas($slas);
                $os->getDevice()->slas()->saveMany($slas);

                // The base RRD
                foreach ($slas as $sla) {
                    $datastore->put($os->getDeviceArray(), 'sla', [
                        'sla_nr' => $sla->sla_nr,
                        'rrd_name' => ['sla', $sla->sla_nr],
                        'rrd_def' => RrdDefinition::make()->addDataset('rtt', 'GAUGE', 0, 300000),
                    ], [
                        'rtt' => $sla->rtt,
                    ]);
                }
            }
        }
    }

    public function dataExists(Device $device): bool
    {
        return $device->slas()->exists();
    }

    /**
     * Remove all DB data for this module.
     * This will be run when the module is disabled.
     */
    public function cleanup(Device $device): int
    {
        return $device->slas()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        return [
            'slas' => $device->slas()->orderBy('sla_nr')
                ->get()->map->makeHidden(['device_id', 'sla_id']),
        ];
    }
}
