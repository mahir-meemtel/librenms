<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Valere extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml
        $device->features = implode(', ', explode(PHP_EOL, snmp_walk($this->getDeviceArray(), 'vpwrModuleOID', '-Oqvs', 'ELTEK-BC2000-DC-POWER-MIB')));
    }
}
