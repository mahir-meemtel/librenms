<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Jetdirect extends Shared\Printer
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml
        $device = $this->getDevice();

        $info = $this->parseDeviceId(snmp_get($this->getDeviceArray(), '.1.3.6.1.4.1.11.2.3.9.1.1.7.0', '-OQv'));
        $hardware = $info['MDL'] ?? $info['MODEL'] ?? $info['DES'] ?? $info['DESCRIPTION'] ?? null;
        if (! empty($hardware)) {
            $hardware = str_ireplace([
                'HP ',
                'Hewlett-Packard ',
                ' Series',
            ], '', $hardware);
            $device->hardware = ucfirst($hardware);
        }
    }
}
