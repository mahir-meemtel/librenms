<?php
namespace ObzoraNMS\OS\Shared;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Foundry extends OS implements ProcessorDiscovery
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $module_descriptions = $this->getCacheByIndex('snAgentConfigModuleDescription', 'FOUNDRY-SN-AGENT-MIB');

        return \SnmpQuery::walk('FOUNDRY-SN-AGENT-MIB::snAgentCpuUtilTable')->mapTable(function ($entry, $slot, $cpu, $interval) use ($module_descriptions) {
            // only discover 5min
            if ($interval == 300) {
                $module_description = '';
                if (isset($module_descriptions[$slot])) {
                    $module_description = $module_descriptions[$slot];
                    [$module_description] = explode(' ', $module_description);
                }

                $descr = "Slot $slot $module_description [$cpu]";
                $index = "$slot.$cpu.$interval";

                if (is_numeric($entry['FOUNDRY-SN-AGENT-MIB::snAgentCpuUtil100thPercent'])) {
                    return Processor::discover(
                        $this->getName(),
                        $this->getDeviceId(),
                        '.1.3.6.1.4.1.1991.1.1.2.11.1.1.6.' . $index,
                        $index,
                        $descr,
                        100,
                        $entry['FOUNDRY-SN-AGENT-MIB::snAgentCpuUtil100thPercent'] / 100
                    );
                } elseif (is_numeric($entry['FOUNDRY-SN-AGENT-MIB::snAgentCpuUtilPercent'])) {
                    return Processor::discover(
                        $this->getName(),
                        $this->getDeviceId(),
                        '.1.3.6.1.4.1.1991.1.1.2.11.1.1.4.' . $index,
                        $index,
                        $descr,
                        1,
                        $entry['FOUNDRY-SN-AGENT-MIB::snAgentCpuUtilPercent']
                    );
                }
            }

            return null;
        })->filter()->values()->all();
    }
}
