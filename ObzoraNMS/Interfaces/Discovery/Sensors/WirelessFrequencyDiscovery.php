<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessFrequencyDiscovery
{
    /**
     * Discover wireless frequency.  This is in MHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency();
}
