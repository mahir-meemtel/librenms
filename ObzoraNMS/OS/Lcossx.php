<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\Interfaces\Polling\ProcessorPolling;
use ObzoraNMS\OS;

class Lcossx extends OS implements ProcessorDiscovery, ProcessorPolling
{
    private string $procOid = '1.3.6.1.4.1.2356.14.1.1.1.24.0';

    // OID string value example: 100ms:87%, 1s:49%, 10s:42%
    private function convertProcessorData(array $input)
    {
        $data = [];
        $cpuList = explode(',', reset($input)[0]);
        foreach ($cpuList as $cpuPart) {
            $cpuValues = explode(':', $cpuPart);
            $cpuName = trim($cpuValues[0]);
            $cpuPerc = str_replace('%', '', $cpuValues[1]);
            $data[$cpuName] = $cpuPerc;
        }

        return $data;
    }

    public function discoverProcessors()
    {
        $data = snmpwalk_array_num($this->getDeviceArray(), $this->procOid);
        if ($data === false) {
            return [];
        }

        $processors = [];
        $count = 0;
        foreach ($this->convertProcessorData($data) as $cpuName => $cpuPerc) {
            $processors[] = Processor::discover(
                'lcossx',
                $this->getDeviceId(),
                $this->procOid,
                $count,
                'Processor ' . $cpuName,
                1,
                $cpuPerc,
                100
            );
            $count++;
        }

        return $processors;
    }

    public function pollProcessors(array $processors)
    {
        $data = snmpwalk_array_num($this->getDeviceArray(), $this->procOid);
        if (get_debug_type($data) != 'array') {
            return [];
        }

        $cpuList = $this->convertProcessorData($data);

        $data = [];
        foreach ($processors as $processor) {
            $processor_id = $processor['processor_id'];
            $key = explode(' ', $processor['processor_descr'])[1];
            $value = $cpuList[$key];
            $data[$processor_id] = $value;
        }

        return $data;
    }
}
