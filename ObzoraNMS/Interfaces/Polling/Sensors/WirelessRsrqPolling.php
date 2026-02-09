<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessRsrqPolling
{
    /**
     * Poll wireless RSRQ (Quality of the received signal) in dB
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessRsrq(array $sensors);
}
