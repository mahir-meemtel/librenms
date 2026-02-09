<?php
namespace ObzoraNMS\OS;

use Illuminate\Support\Str;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Dnos extends OS implements ProcessorDiscovery
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
        $processors = [];

        if (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.6027.1.3')) {
            d_echo('Dell S Series Chassis');
            $this->findProcessors(
                $processors,
                'chStackUnitCpuUtil5Sec',
                'F10-S-SERIES-CHASSIS-MIB',
                '.1.3.6.1.4.1.6027.3.10.1.2.9.1.2',
                'Stack Unit'
            );
        } elseif (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.6027.1.2')) {
            d_echo('Dell C Series Chassis');
            $this->findProcessors(
                $processors,
                'chRpmCpuUtil5Sec',
                'F10-C-SERIES-CHASSIS-MIB',
                '.1.3.6.1.4.1.6027.3.8.1.3.7.1.3',
                'Route Process Module',
                $this->getName() . '-rpm'
            );
            $this->findProcessors(
                $processors,
                'chLineCardCpuUtil5Sec',
                'F10-C-SERIES-CHASSIS-MIB',
                '.1.3.6.1.4.1.6027.3.8.1.5.1.1.1',
                'Line Card'
            );
        } elseif (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.6027.1.4')) {
            d_echo('Dell M Series Chassis');
            $this->findProcessors(
                $processors,
                'chStackUnitCpuUtil5Sec',
                'F10-M-SERIES-CHASSIS-MIB',
                '.1.3.6.1.4.1.6027.3.19.1.2.8.1.2',
                'Stack Unit'
            );
        } elseif (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.6027.1.5')) {
            d_echo('Dell Z Series Chassis');
            $this->findProcessors(
                $processors,
                'chSysCpuUtil5Sec',
                'F10-Z-SERIES-CHASSIS-MIB',
                '.1.3.6.1.4.1.6027.3.25.1.2.3.1.1',
                'Chassis'
            );
        }

        return $processors;
    }

    /**
     * Find processors and append them to the $processors array
     *
     * @param  array  $processors
     * @param  string  $oid  Textual OIDf
     * @param  string  $mib  MIB
     * @param  string  $num_oid  Numerical OID
     * @param  string  $name  Name prefix to display to user
     * @param  string  $type  custom type (if there are multiple in one chassis)
     */
    private function findProcessors(&$processors, $oid, $mib, $num_oid, $name, $type = null)
    {
        $data = $this->getCacheByIndex($oid, $mib);
        foreach ($data as $index => $usage) {
            $processors[] = Processor::discover(
                $type ?: $this->getName(),
                $this->getDeviceId(),
                "$num_oid.$index",
                $index,
                "$name $index CPU",
                1,
                $usage
            );
        }
    }
}
