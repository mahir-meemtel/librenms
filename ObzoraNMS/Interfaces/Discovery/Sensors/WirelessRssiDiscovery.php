<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessRssiDiscovery
{
    /**
     * Discover wireless RSSI (Received Signal Strength Indicator). This is in dBm. Type is rssi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi();
}
