<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class Nitro extends OS
{
    public function discoverOS(Device $device): void
    {
        $this->discoverOS($device); // yaml

        if ($device->sysObjectID == '.1.3.6.1.4.1.23128.1000.1.1') {
            $device->features = 'Enterprise Security Manager';
        } elseif ($device->sysObjectID == '.1.3.6.1.4.1.23128.1000.3.1') {
            $device->features = 'Event Receiver';
        } elseif ($device->sysObjectID == '.1.3.6.1.4.1.23128.1000.7.1') {
            $device->features = 'Enterprise Log Manager';
        } elseif ($device->sysObjectID == '.1.3.6.1.4.1.23128.1000.11.1') {
            $device->features = 'Advanced Correlation Engine';
        } else {
            $device->features = 'Unknown';
        }
    }
}
