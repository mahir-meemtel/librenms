<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Quantastor extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $info = snmp_get_multi_oid($this->getDeviceArray(), 'storageSystem-ServiceVersion.0 hwEnclosure-Vendor.0 hwEnclosure-Model.0 storageSystem-SerialNumber.0', '-OQUs', 'QUANTASTOR-SYS-STATS');
        $device->version = $info['storageSystem-ServiceVersion.0'];
        $device->hardware = $info['hwEnclosure-Vendor.0'] . ' ' . $info['hwEnclosure-Model.0'];
        $device->serial = $info['storageSystem-SerialNumber.0'];
    }
}
