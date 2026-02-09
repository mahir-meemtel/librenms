<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessUtilizationDiscovery
{
    /**
     * Discover wireless utilization.  This is in %. Type is utilization.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessUtilization();
}
