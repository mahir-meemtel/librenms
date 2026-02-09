<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessApCountDiscovery
{
    /**
     * Discover wireless capacity.  This is a percent. Type is capacity.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessApCount();
}
