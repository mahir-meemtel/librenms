<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Smartax extends OS implements ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $proc_oid = '1.3.6.1.4.1.2011.2.6.7.1.1.2.1.5.0';
        $descr_oid = '1.3.6.1.4.1.2011.2.6.7.1.1.2.1.7.0';

        $data = snmpwalk_array_num($this->getDeviceArray(), $proc_oid);
        $descr_data = snmpwalk_array_num($this->getDeviceArray(), $descr_oid);

        // remove first array
        $data = reset($data);
        $descr_data = reset($descr_data);

        $processors = [];
        foreach ($data as $index => $value) {
            if ($value != -1) {
                $proc_desc = $descr_data[$index];
                $processors[] = Processor::discover(
                    'smartax',
                    $this->getDeviceId(),
                    "$proc_oid.$index",
                    $index,
                    "$proc_desc processor",
                    1,
                    $value
                );
            }
        }

        return $processors;
    }
}
