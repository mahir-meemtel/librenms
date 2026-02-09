<?php
namespace ObzoraNMS\Modules;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use App\Models\DiskIo;
use App\Observers\ModuleModelObserver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\RRD\RrdDefinition;
use SnmpQuery;

class UcdDiskio implements Module
{
    use SyncsModels;

    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return [];
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
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    /**
     * @inheritDoc
     */
    public function discover(OS $os): void
    {
        $this->poll($os);
    }

    /**
     * @inheritDoc
     */
    public function poll(OS $os, ?DataStorageInterface $datastore = null): void
    {
        $oids = SnmpQuery::hideMib()->walk('UCD-DISKIO-MIB::diskIOTable')->table(1);
        $ucddisk = new Collection;

        foreach ($oids as $key => $diskData) {
            if (is_array($diskData)) { // invalid snmp response
                if ($this->valid_disk($os, $diskData['diskIODevice']) &&
                    ($diskData['diskIONRead'] > '0' || $diskData['diskIONWritten'] > '0')) {
                    $ucddisk->push(new DiskIo([
                        'diskio_index' => $diskData['diskIOIndex'],
                        'diskio_descr' => $diskData['diskIODevice'],
                    ]));

                    $tags = [
                        'rrd_name' => ['ucd_diskio', $diskData['diskIODevice']],
                        'rrd_def' => RrdDefinition::make()
                            ->addDataset('read', 'DERIVE', 0, 125000000000)
                            ->addDataset('written', 'DERIVE', 0, 125000000000)
                            ->addDataset('reads', 'DERIVE', 0, 125000000000)
                            ->addDataset('writes', 'DERIVE', 0, 125000000000),
                        'descr' => $diskData['diskIODevice'],
                    ];

                    $fields = [
                        'read' => $diskData['diskIONReadX'],
                        'written' => $diskData['diskIONWrittenX'],
                        'reads' => $diskData['diskIOReads'],
                        'writes' => $diskData['diskIOWrites'],
                    ];

                    if ($datastore) {
                        $datastore->put($os->getDeviceArray(), 'ucd_diskio', $tags, $fields);
                    }
                } else {
                    Log::info('Skip Disk: ' . $diskData['diskIODevice']);
                }
            }
        }

        ModuleModelObserver::observe(\App\Models\DiskIo::class);
        $this->syncModels($os->getDevice(), 'diskIo', $ucddisk);
    }

    /**
     * @inheritDoc
     */
    public function dataExists(Device $device): bool
    {
        return $device->diskIo()->exists();
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return $device->diskIo()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        return [
            'disks' => $device->diskIo()
                ->orderBy('diskio_descr')
                ->get()->map->makeHidden(['diskio_id', 'device_id']),
        ];
    }

    private function valid_disk($os, $disk): bool
    {
        foreach (ObzoraConfig::getCombined($os->getDevice()->os, 'bad_disk_regexp') as $bir) {
            if (preg_match($bir . 'i', $disk)) {
                Log::debug('Ignored Disk: ' . $disk . ' (matched: ' . $bir . ')');

                return false;
            }
        }

        return true;
    }
}
