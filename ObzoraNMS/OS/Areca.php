<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;
use ObzoraNMS\Util\StringHelpers;

class Areca extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); //yaml

        // Sometimes firmware outputs serial as hex-string
        if (StringHelpers::isHex($device->serial, ' ')) {
            $device->serial = StringHelpers::hexToAscii($device->serial, ' ');
        }
    }
}
