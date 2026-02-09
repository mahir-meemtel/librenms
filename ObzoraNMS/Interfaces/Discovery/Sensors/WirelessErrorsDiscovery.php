<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessErrorsDiscovery
{
    /**
     * Discover wireless bit errors.  This is in total bits. Type is errors.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessErrors();
}
