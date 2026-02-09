<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class PbnCp extends OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        // normalize MAC address (serial)
        $device->serial = str_replace([' ', ':', '-', '"'], '', $device->serial);
    }
}
