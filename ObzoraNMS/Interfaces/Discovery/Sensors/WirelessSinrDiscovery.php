<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessSinrDiscovery
{
    /**
     * Discover wireless SINR.  This is in dB. Type is sinr.
     * Signal-to-Interference-plus-Noise Ratio
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSinr();
}
