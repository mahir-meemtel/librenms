<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Ipolis extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        $oids = [
            'hardware' => $device->sysObjectID . '.1.0',
            'version' => $device->sysObjectID . '.2.1.1.0',
        ];

        $os_data = \SnmpQuery::get($oids)->values();

        $device->hardware = $os_data[$oids['hardware']] ?? null;
        $device->version = $os_data[$oids['version']] ?? null;
    }
}
