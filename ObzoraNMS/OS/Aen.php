<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Aen extends OS implements ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $device = $this->getDeviceArray();

        // don't poll v5.3.1_22558 devices due to bug that crashes snmpd
        if ($device['version'] == 'AEN_5.3.1_22558') {
            return [];
        }

        return [
            Processor::discover(
                $this->getName(),
                $this->getDeviceId(),
                '.1.3.6.1.4.1.22420.1.1.20.0', // ACD-DESC-MIB::acdDescCpuUsageCurrent
                0
            ),
        ];
    }
}
