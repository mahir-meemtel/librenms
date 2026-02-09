<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessChannelDiscovery
{
    /**
     * Discover Wireless channel in channel number. Type is channel.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessChannel();
}
