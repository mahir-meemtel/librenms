<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessPowerDiscovery
{
    /**
     * Discover wireless tx or rx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower();
}
