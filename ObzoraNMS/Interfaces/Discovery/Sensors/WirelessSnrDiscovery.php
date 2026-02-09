<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessSnrDiscovery
{
    /**
     * Discover wireless SNR.  This is in dB. Type is snr.
     * Formula: SNR = Signal or Rx Power - Noise Floor
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSnr();
}
