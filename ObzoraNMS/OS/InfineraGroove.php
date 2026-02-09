<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class InfineraGroove extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        $oid_list = [
            'neType.0',
            'softwareloadSwloadState.1',
            'softwareloadSwloadState.2',
            'softwareloadSwloadVersion.1',
            'softwareloadSwloadVersion.2',
            'inventoryManufacturerNumber.shelf.1.0.0.0',
        ];

        $data = snmp_get_multi($this->getDeviceArray(), $oid_list, '-OUQs', 'CORIANT-GROOVE-MIB');

        foreach ($data as $value) {
            if (isset($value['softwareloadSwloadState']) && $value['softwareloadSwloadState'] == 'active') {
                $device->version = $value['softwareloadSwloadVersion'];
                break;
            }
        }
        $device->hardware = $data[0]['neType'] ?? null;
        $device->serial = $data['shelf.1.0.0.0']['inventoryManufacturerNumber'] ?? null;
    }
}
