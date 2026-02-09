<?php
namespace ObzoraNMS\OS;

use App\Models\Mempool;
use Illuminate\Support\Collection;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\MempoolsDiscovery;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Scalance extends OS implements MempoolsDiscovery, ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $oid = '.1.3.6.1.4.1.4329.20.1.1.1.1.79.3.1.13.0';

        return [
            Processor::discover(
                'scalance-cpu',
                $this->getDeviceId(),
                $oid,
                0,
                'Processor',
            ),
        ];
    }

    public function discoverMempools()
    {
        $perc_oid = '.1.3.6.1.4.1.4329.20.1.1.1.1.79.3.1.13.1';
        $warn_oid = '.1.3.6.1.4.1.4329.20.1.1.1.1.79.3.1.16.1';
        $mempool_data = snmp_get_multi_oid($this->getDeviceArray(), [$perc_oid, $warn_oid]);

        if ($mempool_data[$perc_oid] === false) {
            return new Collection();
        }

        return collect()->push((new Mempool([
            'mempool_index' => 0,
            'mempool_type' => 'scalance',
            'mempool_class' => 'system',
            'mempool_descr' => 'Memory',
            'mempool_perc_oid' => $perc_oid,
            'mempool_perc_warn' => $mempool_data[$warn_oid],
        ]))->fillUsage(null, null, null, $mempool_data[$perc_oid]));
    }
}
