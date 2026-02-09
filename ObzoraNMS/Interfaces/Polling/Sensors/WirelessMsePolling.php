<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessMsePolling
{
    /**
     * Poll wireless MSE in dB
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessMse(array $sensors);
}
