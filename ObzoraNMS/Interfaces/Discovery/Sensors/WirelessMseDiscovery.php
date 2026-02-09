<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessMseDiscovery
{
    /**
     * Discover wireless MSE. Mean square error value in dB. Type is mse.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessMse();
}
