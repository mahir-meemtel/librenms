<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use SnmpQuery;

class IbmImm extends \ObzoraNMS\OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $device->features = implode(' ', SnmpQuery::walk('IMM-MIB::immVpdType')->pluck());
    }
}
