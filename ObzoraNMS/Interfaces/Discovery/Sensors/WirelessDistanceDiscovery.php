<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessDistanceDiscovery
{
    /**
     * Discover wireless distance.  This is in Kilometers. Type is distance.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessDistance();
}
