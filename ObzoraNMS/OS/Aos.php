<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Aos extends OS implements ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $processor = Processor::discover(
            'aos-system',
            $this->getDeviceId(),
            '.1.3.6.1.4.1.6486.800.1.2.1.16.1.1.1.13.0', // ALCATEL-IND1-HEALTH-MIB::healthDeviceCpuLatest
            0,
            'Device CPU'
        );

        if (! $processor->isValid()) {
            // AOS7 devices use a different OID for CPU load. Not all Switches have
            // healthModuleCpuLatest so we use healthModuleCpu1MinAvg which makes no
            // difference for a 5 min. polling interval.
            // Note: This OID shows (a) the CPU load of a single switch or (b) the
            // average CPU load of all CPUs in a stack of switches.
            $processor = Processor::discover(
                'aos-system',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.6486.801.1.2.1.16.1.1.1.1.1.11.0',
                0,
                'Device CPU'
            );
        }

        return [$processor];
    }
}
