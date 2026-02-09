<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessUtilizationPolling
{
    /**
     * Poll wireless utilization
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessUtilization(array $sensors);
}
