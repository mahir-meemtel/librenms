<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\Interfaces\Polling\ProcessorPolling;
use ObzoraNMS\OS;

class Viptela extends OS implements ProcessorDiscovery, ProcessorPolling
{
    private string $procOid = '.1.3.6.1.4.1.41916.11.1.16.0';

    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml
    }

    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processor
     */
    public function discoverProcessors()
    {
        $idle_cpu = 100 - (int) \SnmpQuery::get([$this->procOid])->value();
        $processors[] = Processor::discover(
            'viptela',
            $this->getDeviceId(),
            $this->procOid,
            0,
            'Processor',
            1,
            $idle_cpu,
        );

        return $processors;
    }

    /**
     * Poll processor data.  This can be implemented if custom polling is needed.
     *
     * @param  array  $processors  Array of processor entries from the database that need to be polled
     * @return array of polled data
     */
    public function pollProcessors(array $processors)
    {
        $data = [];

        foreach ($processors as $processor) {
            $data[$processor['processor_id']] = 100 - (int) \SnmpQuery::get([$this->procOid])->value();
        }

        return $data;
    }
}
