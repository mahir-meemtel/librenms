<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;

class Beagleboard extends Linux implements OSDiscovery
{
    /**
     * Retrieve basic information about the OS / device
     */
    public function discoverOS(Device $device): void
    {
        $oids = ['NET-SNMP-EXTEND-MIB::nsExtendOutputFull."distro"', 'NET-SNMP-EXTEND-MIB::nsExtendOutputFull."hardware"'];
        $info = snmp_get_multi($this->getDeviceArray(), $oids);
        $device->version = str_replace('BeagleBoard.org ', '', $info['"distro"']['nsExtendOutputFull']);
        $device->hardware = $info['"hardware"']['nsExtendOutputFull'];
    }
}
