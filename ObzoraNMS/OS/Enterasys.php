<?php
namespace ObzoraNMS\OS;

use App\Models\Mempool;
use App\Models\Storage;
use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Discovery\MempoolsDiscovery;
use SnmpQuery;

class Enterasys extends \ObzoraNMS\OS implements MempoolsDiscovery
{
    public function discoverMempools()
    {
        $mempools = new Collection();
        $mem = snmpwalk_group($this->getDeviceArray(), 'etsysResourceStorageTable', 'ENTERASYS-RESOURCE-UTILIZATION-MIB', 3);

        foreach ($mem as $index => $mem_data) {
            foreach ($mem_data['ram'] ?? [] as $mem_id => $ram) {
                $descr = $ram['etsysResourceStorageDescr'];
                if ($index > 1000) {
                    $descr = 'Slot #' . substr($index, -1) . " $descr";
                }

                $mempools->push((new Mempool([
                    'mempool_index' => $index,
                    'mempool_type' => 'enterasys',
                    'mempool_class' => 'system',
                    'mempool_descr' => $descr,
                    'mempool_precision' => 1024,
                    'mempool_free_oid' => ".1.3.6.1.4.1.5624.1.2.49.1.3.1.1.5.$index.2.$mem_id",
                    'mempool_perc_warn' => 90,
                ]))->fillUsage(null, $ram['etsysResourceStorageSize'] ?? null, $ram['etsysResourceStorageAvailable'] ?? null));
            }
        }

        return $mempools;
    }

    public function discoverStorage(): Collection
    {
        $storage = new Collection;

        $free = SnmpQuery::get('ENTERASYS-RESOURCE-UTILIZATION-MIB::etsysResourceStorageAvailable.3.flash.0')->value();
        $total = SnmpQuery::get('ENTERASYS-RESOURCE-UTILIZATION-MIB::etsysResourceStorageSize.3.flash.0')->value();
        if (is_numeric($free) && is_numeric($total)) {
            $storage->push((new Storage([
                'type' => 'enterasys',
                'storage_type' => 'Flash',
                'storage_descr' => 'Internal Flash Storage',
                'storage_units' => 1024,
                'storage_index' => 0,
                'storage_free_oid' => '.1.3.6.1.4.1.5624.1.2.49.1.3.1.1.5.3.3.0',
            ]))->fillUsage(total: $total, free: $free));
        }

        return $storage;
    }
}
