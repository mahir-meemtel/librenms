<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessCapacityPolling
{
    /**
     * Poll wireless capacity as a percent
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessCapacity(array $sensors);
}
