<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Cumulus extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        $response = \SnmpQuery::next([
            'ENTITY-MIB::entPhysicalDescr',
            'ENTITY-MIB::entPhysicalSoftwareRev',
            'ENTITY-MIB::entPhysicalSerialNum',
        ]);

        $device->hardware = $response->value('ENTITY-MIB::entPhysicalDescr') ?: null;
        $device->serial = $response->value('ENTITY-MIB::entPhysicalSerialNum') ?: null;
        $device->version = preg_replace('/^Cumulus Linux /', '', $response->value('ENTITY-MIB::entPhysicalSoftwareRev')) ?: null;
    }
}
