<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessRateDiscovery
{
    /**
     * Discover wireless rate. This is in bps. Type is rate.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRate();
}
