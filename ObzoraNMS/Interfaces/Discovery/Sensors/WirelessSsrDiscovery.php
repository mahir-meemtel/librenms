<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessSsrDiscovery
{
    /**
     * Discover wireless SSR.  This is in dB. Type is ssr.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSsr();
}
