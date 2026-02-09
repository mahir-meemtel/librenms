<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Xerox extends Shared\Printer
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $info = $this->parseDeviceId(snmp_get($this->getDeviceArray(), '.1.3.6.1.4.1.253.8.51.1.2.1.20.1', '-OQv'));
        $device->hardware = $info['MDL'] ?? $info['DES'] ?? $device->hardware;
    }
}
