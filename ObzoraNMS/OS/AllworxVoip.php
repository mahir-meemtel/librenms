<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class AllworxVoip extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $device->hardware = $device->sysDescr;
        $device->serial = $device->sysName;
        $device->version = snmp_get($this->getDeviceArray(), 'applVersion.1', '-OQv', 'NETWORK-SERVICES-MIB');
    }
}
