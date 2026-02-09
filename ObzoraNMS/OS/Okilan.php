<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Okilan extends Jetdirect
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // jetdirect
        $device->hardware = str_replace('OKI ', '', $device->hardware); // remove useless brand
    }
}
