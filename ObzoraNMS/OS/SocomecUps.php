<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class SocomecUps extends OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $data = intval(\SnmpQuery::get('SOCOMECUPS-MIB::upsConfigNomKva.0')->value());
        $device->features = ($data / 10) . ' kVA';
    }
}
