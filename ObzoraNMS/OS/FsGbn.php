<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class FsGbn extends OS implements ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $processors = [];

        // Test first pair of OIDs from GBNPlatformOAM-MIB
        $processors_data = snmpwalk_cache_oid($this->getDeviceArray(), 'cpuDescription', [], 'GBNPlatformOAM-MIB', 'fs');
        $processors_data = snmpwalk_cache_oid($this->getDeviceArray(), 'cpuIdle', $processors_data, 'GBNPlatformOAM-MIB', 'fs');
        foreach ($processors_data as $index => $entry) {
            $processors[] = Processor::discover(
                $this->getName(),
                $this->getDeviceId(),
                '.1.3.6.1.4.1.13464.1.2.1.1.2.11.' . $index, //GBNPlatformOAM-MIB::cpuIdle.0 = INTEGER: 95
                $index,
                $entry['cpuDescription'],
                -1,
                100 - $entry['cpuIdle']
            );
        }

        return $processors;
    }
}
