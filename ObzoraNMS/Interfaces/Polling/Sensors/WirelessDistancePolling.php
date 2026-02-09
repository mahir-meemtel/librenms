<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessDistancePolling
{
    /**
     * Poll wireless frequency as kilometers
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessDistance(array $sensors);
}
