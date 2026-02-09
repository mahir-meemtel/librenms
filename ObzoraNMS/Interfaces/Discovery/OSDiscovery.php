<?php
namespace ObzoraNMS\Interfaces\Discovery;

use App\Models\Device;

interface OSDiscovery
{
    /**
     * Discover additional information about the OS.
     * Primarily this is just version, hardware, features, serial, but could be anything
     *
     * @param  Device  $device
     */
    public function discoverOS(Device $device): void;
}
