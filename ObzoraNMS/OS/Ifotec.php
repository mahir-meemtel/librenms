<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Ifotec extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        if (Str::startsWith($device->sysObjectID, '.1.3.6.1.4.1.21362.100.')) {
            $ifoSysProductIndex = snmp_get($this->getDeviceArray(), 'ifoSysProductIndex.0', '-Oqv', 'IFOTEC-SMI');

            if ($ifoSysProductIndex !== false) {
                $oids = [
                    'ifoSysSerialNumber.' . $ifoSysProductIndex,
                    'ifoSysFirmware.' . $ifoSysProductIndex,
                    'ifoSysBootloader.' . $ifoSysProductIndex,
                ];
                $data = snmp_get_multi($this->getDeviceArray(), $oids, ['-OQUs'], 'IFOTEC-SMI');

                $device->version = $data[1]['ifoSysFirmware'] . ' (Bootloader ' . $data[1]['ifoSysBootloader'] . ')';
                $device->serial = $data[1]['ifoSysSerialNumber'];
            }
        }

        // sysDecr struct = (<product_reference> . ' : ' . <product_description>) OR (<product_reference>)
        [$device->hardware] = explode(' : ', $device->sysDescr, 2);
    }
}
