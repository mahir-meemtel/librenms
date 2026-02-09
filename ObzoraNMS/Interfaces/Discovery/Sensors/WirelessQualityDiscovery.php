<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessQualityDiscovery
{
    /**
     * Discover wireless quality.  This is a percent. Type is quality.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessQuality();
}
