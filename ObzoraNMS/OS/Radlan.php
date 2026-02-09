<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Radlan extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        $device->hardware = snmp_getnext($this->getDeviceArray(), 'entPhysicalDescr.64', '-OsvQU', 'ENTITY-MIB');
        $device->version = snmp_get($this->getDeviceArray(), 'rndBrgVersion.0', '-OsvQU', 'RADLAN-MIB');
        $device->serial = snmp_getnext($this->getDeviceArray(), 'entPhysicalSerialNum.64', '-OsvQU', 'ENTITY-MIB') ?: null;
    }
}
