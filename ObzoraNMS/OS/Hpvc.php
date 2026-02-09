<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class Hpvc extends OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        // Serial number is in sysName after string "VCEX"
        $device->serial = substr($device->sysName, 4);
    }
}
