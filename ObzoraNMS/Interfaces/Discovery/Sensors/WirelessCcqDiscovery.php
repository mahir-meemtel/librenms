<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessCcqDiscovery
{
    /**
     * Discover wireless client connection quality.  This is a percent. Type is ccq.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessCcq();
}
