<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Pbn extends OS implements ProcessorDiscovery
{
    public function __construct(&$device)
    {
        parent::__construct($device);

        if (preg_match('/^.* Build (?<build>\d+)/', (string) $this->getDevice()->version, $version)) {
            if ($version['build'] <= 16607) { // Buggy version :-(
                $this->stpTimeFactor = 1;
            }
        }
    }

    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors(): array
    {
        return [
            Processor::discover(
                'pbn-cpu',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.11606.10.9.109.1.1.1.1.5.1', // NMS-PROCESS-MIB::nmspmCPUTotal5min
                0
            ),
        ];
    }
}
