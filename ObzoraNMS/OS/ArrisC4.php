<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class ArrisC4 extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $device = $this->getDevice();

        preg_match("/(CMTS|CER)_V([\d.]+),/", $device->sysDescr, $match);
        $device->version = $match[2];

        switch ($device->sysObjectID) {
            case '.1.3.6.1.4.1.4998.2.1':
                $device->hardware = 'C4';
                break;
            case '.1.3.6.1.4.1.4998.2.2':
                $device->hardware = 'C4c';
                break;
            case '.1.3.6.1.4.1.4115.1.9.1':
                $device->hardware = 'E6000';
                break;
        }
    }
}
