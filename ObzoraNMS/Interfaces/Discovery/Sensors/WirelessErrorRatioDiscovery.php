<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessErrorRatioDiscovery
{
    /**
     * Discover wireless bit/packet error ratio.  This is in percent. Type is error-ratio.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessErrorRatio();
}
