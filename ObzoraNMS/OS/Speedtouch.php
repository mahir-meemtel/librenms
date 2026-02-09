<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class Speedtouch extends OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        // Filthy hack to get software version. may not work on anything but 585v7 :)
        $loop = \SnmpQuery::get('IF-MIB::ifDescr.101')->value();

        if ($loop) {
            preg_match('@([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)@i', $loop, $matches);
            $device->version = $matches[1];
        }
    }
}
