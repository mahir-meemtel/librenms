<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessRsrqDiscovery
{
    /**
     * Discover wireless RSRQ (Quality of the received signal,). This is in dB. Type is rsrq.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRsrq();
}
