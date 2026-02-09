<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessErrorRateDiscovery
{
    /**
     * Discover wireless bit error rate.  This is in bps. Type is error-rate.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessErrorRate();
}
