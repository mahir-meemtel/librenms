<?php
namespace ObzoraNMS\OS\Traits;

use ObzoraNMS\Device\Processor;

trait VxworksProcessorUsage
{
    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @param  string  $oid  Custom OID to fetch from
     * @return array Processors
     */
    public function discoverProcessors($oid = '.1.3.6.1.4.1.4413.1.1.1.1.4.9.0')
    {
        $usage = snmp_get($this->getDeviceArray(), $oid, '-Ovq');
        if ($usage) {
            $usage = $this->parseCpuUsage($usage);
            if (is_numeric($usage)) {
                return [
                    Processor::discover(
                        $this->getName(),
                        $this->getDeviceId(),
                        $oid,
                        0,
                        'Processor',
                        1,
                        $usage
                    ),
                ];
            }
        }

        return [];
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
            $data[$processor['processor_id']] = $this->parseCpuUsage(
                snmp_get($this->getDeviceArray(), $processor['processor_oid'])
            );
        }

        return $data;
    }

    /**
     * Parse the silly cpu usage string
     * "    5 Secs ( 96.4918%)   60 Secs ( 54.2271%)  300 Secs ( 38.2591%)"
     *
     * @param  string  $data
     * @return mixed
     */
    private function parseCpuUsage($data)
    {
        preg_match('/([0-9]+.[0-9]+)%/', $data, $matches);

        return $matches[1];
    }
}
