<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessRssiPolling
{
    /**
     * Poll wireless RSSI (Received Signal Strength Indicator) in dBm
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessRssi(array $sensors);
}
