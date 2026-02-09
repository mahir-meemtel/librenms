<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\Processor;
use ObzoraNMS\OS;

class FsSwitch extends OS
{
    public static function normalizeTransceiverValues($value): float
    {
        // Convert fixed-point integer thresholds to float
        $type = gettype($value);
        if ($type === 'integer') {
            // Thresholds are integers
            $value /= 100.0;
        }

        return $value;
    }

    public static function normalizeTransceiverValuesCurrent($value): float
    {
        $value = FsSwitch::normalizeTransceiverValues($value);
        $value *= 0.001; // mA to A

        return $value;
    }

    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $processors = [];

        // Tests OID from SWITCH MIB.
        $processors_data = snmpwalk_cache_oid($this->getDeviceArray(), 'ssCpuIdle', [], 'SWITCH', 'fs');

        foreach ($processors_data as $index => $entry) {
            $processors[] = Processor::discover(
                'fs-SWITCHMIB',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.27975.1.2.11.' . $index,
                $index,
                'CPU',
                -1,
                100 - $entry['ssCpuIdle']
            );
        }

        return $processors;
    }
}
