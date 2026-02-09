<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use App\Models\Mempool;
use Illuminate\Support\Collection;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\MempoolsDiscovery;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Dlinkap extends OS implements MempoolsDiscovery, ProcessorDiscovery
{
    public function discoverOS(Device $device): void
    {
        $firmware_oid = $device->sysObjectID . '.5.1.1.0';
        $hardware_oid = $device->sysObjectID . '.5.1.5.0';

        $device->version = snmp_get($this->getDeviceArray(), $firmware_oid, '-Oqv') ?: null;
        $device->hardware = trim($device->sysDescr . ' ' . snmp_get($this->getDeviceArray(), $hardware_oid, '-Oqv'));
    }

    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        return [
            Processor::discover(
                'dlinkap-cpu',
                $this->getDeviceId(),
                $this->getDevice()->sysObjectID . '.5.1.3.0',  // different OID for each model
                0,
                'Processor',
                100
            ),
        ];
    }

    public function discoverMempools()
    {
        $oid = $this->getDevice()->sysObjectID . '.5.1.4.0';
        $memory = snmp_get($this->getDeviceArray(), $oid, '-OQv');

        if ($memory === false) {
            return new Collection();
        }

        return collect()->push((new Mempool([
            'mempool_index' => 0,
            'mempool_type' => 'dlinkap',
            'mempool_class' => 'system',
            'mempool_descr' => 'Memory',
            'mempool_perc_oid' => $oid,
        ]))->fillUsage(null, null, null, $memory));
    }
}
