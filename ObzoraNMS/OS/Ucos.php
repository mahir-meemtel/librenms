<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use Illuminate\Support\Str;

class Ucos extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $applist = snmp_walk($this->getDeviceArray(), 'SYSAPPL-MIB::sysApplInstallPkgProductName', '-OQv');
        if (Str::contains($applist, 'Cisco Unified CCX Database')) {
            $device->features = 'UCCX';
        } elseif (Str::contains($applist, 'Cisco CallManager')) {
            $device->features = 'CUCM';
        } elseif (Str::contains($applist, 'Cisco Emergency Responder')) {
            $device->features = 'CER';
        } elseif (Str::contains($applist, 'Connection System Agent')) {
            $device->features = 'CUC';
        }
    }
}
