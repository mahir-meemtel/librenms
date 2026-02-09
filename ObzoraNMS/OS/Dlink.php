<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Dlink extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        if (! empty($device->hardware) && $rev = snmp_get($this->getDeviceArray(), '.1.3.6.1.2.1.16.19.3.0', '-Oqv')) {
            $device->hardware .= ' Rev. ' . $rev;
        }
    }
}
