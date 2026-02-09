<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;

class Iosxr extends Shared\Cisco implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $device->serial = $this->getMainSerial();

        if (preg_match('/^Cisco IOS XR Software \(Cisco ([^\)]+)\),\s+Version ([^\[]+)\[([^\]]+)\]/', $device->sysDescr, $regexp_result)) {
            $device->hardware = $regexp_result[1];
            $device->features = $regexp_result[3];
            $device->version = $regexp_result[2];
        } elseif (preg_match('/^Cisco IOS XR Software \(([^\)]+)\),\s+Version\s+([^\s]+)/', $device->sysDescr, $regexp_result)) {
            $device->hardware = $regexp_result[1];
            $device->version = $regexp_result[2];
        }

        $oids = ['entPhysicalSoftwareRev.1', 'entPhysicalModelName.8384513', 'entPhysicalModelName.8384518'];
        $data = snmp_get_multi($this->getDeviceArray(), $oids, '-OQUs', 'ENTITY-MIB');

        if (! empty($data[1]['entPhysicalSoftwareRev'])) {
            $device->version = $data[1]['entPhysicalSoftwareRev'];
        }

        if (! empty($data[8384513]['entPhysicalModelName'])) {
            $device->hardware = $data[8384513]['entPhysicalModelName'];
        } elseif (! empty($data[8384518]['entPhysicalModelName'])) {
            $device->hardware = $data[8384518]['entPhysicalModelName'];
        }
    }
}
