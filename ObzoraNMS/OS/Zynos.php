<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS\Shared\Zyxel;

class Zynos extends Zyxel implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        // if not already set, let's fill the gaps
        if (empty($device->hardware)) {
            $device->hardware = $device->sysDescr;
        }

        if (empty($device->serial)) {
            $serial_oids = [
                '.1.3.6.1.4.1.890.1.5.8.20.1.10.0', // ZYXEL-GS4012F-MIB::sysSerialNumber.0
                '.1.3.6.1.4.1.890.1.5.8.47.1.10.0', // ZYXEL-MGS3712-MIB::sysSerialNumber.0
                '.1.3.6.1.4.1.890.1.5.8.55.1.10.0', // ZYXEL-GS2200-24-MIB::sysSerialNumber.0
            ];
            $serials = snmp_get_multi_oid($this->getDeviceArray(), $serial_oids);

            foreach ($serial_oids as $oid) {
                if (! empty($serials[$oid])) {
                    $device->serial = $serials[$oid];
                    break;
                }
            }
        }
    }
}
