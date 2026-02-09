<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessRsrpDiscovery
{
    /**
     * Discover wireless RSRP (Reference Signal Received Power). This is in dBm. Type is rsrp.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRsrp();
}
