<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class Svos extends OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'raidExMibRaidListTable', [], 'HM800MIB');

        foreach ($data as $serialnum => $oid) {
            if (! empty($data[$serialnum]['raidlistSerialNumber'])) {
                $device->serial = $data[$serialnum]['raidlistSerialNumber'];
            }

            if (! empty($data[$serialnum]['raidlistDKCProductName'])) {
                $device->hardware = $data[$serialnum]['raidlistDKCProductName'];
            }

            if (! empty($data[$serialnum]['raidlistDKCMainVersion'])) {
                $device->version = $data[$serialnum]['raidlistDKCMainVersion'];
            }
        }
    }
}
