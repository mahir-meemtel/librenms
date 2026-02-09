<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;
use SnmpQuery;

class Qnap extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $response = SnmpQuery::next([
            'NAS-MIB::enclosureModel',
            'NAS-MIB::enclosureSerialNum',
            'ENTITY-MIB::entPhysicalFirmwareRev',
        ]);

        $device->version = trim($response->value('ENTITY-MIB::entPhysicalFirmwareRev'), '\"') ?: null;
        $device->hardware = $response->value('NAS-MIB::enclosureModel') ?: null;
        $device->serial = $response->value('NAS-MIB::enclosureSerialNum') ?: null;
    }
}
