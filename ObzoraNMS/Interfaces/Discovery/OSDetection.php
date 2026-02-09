<?php
namespace ObzoraNMS\Interfaces\Discovery;

use App\Models\Device;

interface OSDetection
{
    /**
     * Check if the give device is this OS.
     * $device->sysObjectID and $device->sysDescr will be pre-populated
     * Please avoid additional snmp queries if possible
     *
     * @param  Device  $device
     * @return bool
     */
    public static function detectOS(Device $device): bool;
}
