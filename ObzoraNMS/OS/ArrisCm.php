<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class ArrisCm extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        preg_match('/<<HW_REV: (?<rev>.+); VENDOR:.*SW_REV: (?<version>.+); MODEL: (?<hardware>.+)>>/', $device->sysDescr, $matches);

        $device->hardware = "{$matches['hardware']} (Rev: {$matches['rev']})";
        $device->version = $matches['version'];
    }
}
