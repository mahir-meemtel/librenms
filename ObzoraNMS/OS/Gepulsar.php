<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;
use SnmpQuery;

class Gepulsar extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $info = SnmpQuery::get([
            'NE843-MIB::ne843Ps1Sn.0',
            'NE843-MIB::ne843Ps1Verw.0',
            'NE843-MIB::ne843Ps1Brc.0',
        ])->values();

        $device->version = $info['NE843-MIB::ne843Ps1Verw.0'] ?? null;
        $device->hardware = $info['NE843-MIB::ne843Ps1Brc.0'] ?? null;
        $device->serial = $info['NE843-MIB::ne843Ps1Sn.0'] ?? null;
    }
}
