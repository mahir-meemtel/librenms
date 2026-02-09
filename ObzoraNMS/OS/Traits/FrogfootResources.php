<?php
namespace ObzoraNMS\OS\Traits;

use ObzoraNMS\Device\Processor;

trait FrogfootResources
{
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
                $this->getName(),
                $this->getDeviceId(),
                '1.3.6.1.4.1.10002.1.1.1.4.2.1.3.2',
                0
            ),
        ];
    }
}
