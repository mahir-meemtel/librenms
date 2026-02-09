<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Apsoluteos extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $oids = ['genGroupHWVersion.0', 'rndSerialNumber.0', 'rndApsoluteOSVersion.0', 'rdwrDevicePortsConfig.0'];
        $data = snmp_get_multi($this->getDeviceArray(), $oids, '-OQs', 'RADWARE-MIB');

        $device->serial = $data[0]['rndSerialNumber'] ?? null;
        $device->version = $data[0]['rndApsoluteOSVersion'] ?? null;
        $device->hardware = $data[0]['genGroupHWVersion'] ?? null;
        if (isset($data[0]['rdwrDevicePortsConfig'])) {
            $device->features = 'Ver. ' . $data[0]['rdwrDevicePortsConfig'];
        }
    }
}
