<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Netsure extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $device->version = snmp_getnext($this->getDeviceArray(), 'vecFirmwareVersion', '-Oqv', 'VEC-MIBv5-9');
    }
}
