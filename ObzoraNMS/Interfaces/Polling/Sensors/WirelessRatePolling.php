<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessRatePolling
{
    /**
     * Poll wireless rates in bps
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessRate(array $sensors);
}
