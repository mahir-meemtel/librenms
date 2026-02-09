<?php
namespace ObzoraNMS\OS;

use Illuminate\Support\Str;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Sonicwall extends OS implements ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        if (Str::startsWith($this->getDeviceArray()['sysObjectID'], '.1.3.6.1.4.1.8741.1')) {
            return [
                Processor::discover(
                    'sonicwall',
                    $this->getDeviceId(),
                    '.1.3.6.1.4.1.8741.1.3.1.3.0',  // SONICWALL-FIREWALL-IP-STATISTICS-MIB::sonicCurrentCPUUtil.0
                    0,
                    'CPU',
                    1
                ),
            ];
        } else {
            return [
                Processor::discover(
                    'sonicwall',
                    $this->getDeviceId(),
                    $this->getDeviceArray()['sysObjectID'] . '.2.1.3.0',  // different OID for each model
                    0,
                    'CPU',
                    1
                ),
            ];
        }
    }
}
