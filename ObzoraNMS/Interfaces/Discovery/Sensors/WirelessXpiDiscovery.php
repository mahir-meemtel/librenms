<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessXpiDiscovery
{
    /**
     * Discover wireless Cross Polar Interference.  Measured in dB. Type is xpi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessXpi();
}
