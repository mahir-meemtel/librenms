<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessNoiseFloorDiscovery
{
    /**
     * Discover wireless noise floor. This is in dBm/Hz. Type is noise-floor.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessNoiseFloor();
}
