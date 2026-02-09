<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class MoxaEtherdevice extends OS implements ProcessorDiscovery
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

        // Moxa people enjoy creating MIBs for each model!
        // .1.3.6.1.4.1.8691.7.116.1.54.0 = MOXA-IKS6726A-MIB::cpuLoading30s.0
        // .1.3.6.1.4.1.8691.7.69.1.54.0 = MOXA-EDSG508E-MIB::cpuLoading30s.0
        $oid = $device['sysObjectID'] . '.1.54.0';

        return [
            Processor::discover(
                $this->getName(),
                $this->getDeviceId(),
                $oid,
                0
            ),
        ];
    }
}
