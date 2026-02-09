<?php
namespace ObzoraNMS\OS\Shared;

use App\Models\Device;
use ObzoraNMS\OS;
use ObzoraNMS\OS\Traits\YamlOSDiscovery;

class Zyxel extends OS
{
    use YamlOSDiscovery {
        YamlOSDiscovery::discoverOS as discoverYamlOS;
    }

    public function discoverOS(Device $device): void
    {
        // yaml discovery overrides this
        if ($this->hasYamlDiscovery('os')) {
            $this->discoverYamlOS($device);

            return;
        }

        $oids = [
            '.1.3.6.1.4.1.890.1.15.3.1.11.0', // ZYXEL-ES-COMMON::sysProductModel.0
            '.1.3.6.1.4.1.890.1.15.3.1.6.0', // ZYXEL-ES-COMMON::sysSwVersionString.0
            '.1.3.6.1.4.1.890.1.15.3.1.12.0', // ZYXEL-ES-COMMON::sysProductSerialNumber.0
            // ZYXEL-ES-ZyxelAPMgmt::operationMode.0
        ];
        $data = snmp_get_multi_oid($this->getDeviceArray(), $oids, '-OUQnt');
        if (empty($data)) {
            return;
        }
        $device->hardware = $data['.1.3.6.1.4.1.890.1.15.3.1.11.0'] ?? null;
        [$device->version] = explode(' | ', $data['.1.3.6.1.4.1.890.1.15.3.1.6.0'] ?? null);
        $device->serial = $data['.1.3.6.1.4.1.890.1.15.3.1.12.0'] ?? null;
    }
}
