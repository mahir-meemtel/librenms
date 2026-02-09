<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use Illuminate\Support\Str;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Terra extends OS implements ProcessorDiscovery, OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $models = [
            'sda410C' => '5',
            'sta410C' => '6',
            'saa410C' => '7',
            'sti410C' => '9',
            'sai410C' => '10',
            'ttd440' => '14',
            'ttx410C' => '15',
            'tdx410C' => '16',
            'sti440' => '18',
        ];

        foreach ($models as $model => $index) {
            if (Str::contains($device->sysDescr, $model)) {
                $oid_terra = '.1.3.6.1.4.1.30631.1.';
                $oid = [$oid_terra . $index . '.4.1.0', $oid_terra . $index . '.4.2.0'];

                $data = snmp_get_multi_oid($device, $oid);
                $device->hardware = $model;
                $device->version = $data[$oid[0]] ?? null;
                $device->version = $data[$oid[1]] ?? null;
                break;
            }
        }
    }

    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $device = $this->getDeviceArray();

        $query = [
            'sti410C' => '.1.3.6.1.4.1.30631.1.9.1.1.3.0',
            'sti440' => '.1.3.6.1.4.1.30631.1.18.1.326.3.0',
        ];

        foreach ($query as $decr => $oid) {
            if (strpos($device['sysDescr'], $decr) !== false) {
                return [
                    Processor::discover(
                        'cpu',
                        $this->getDeviceId(),
                        $oid,
                        0
                    ),
                ];
            }
        }

        return [];
    }
}
