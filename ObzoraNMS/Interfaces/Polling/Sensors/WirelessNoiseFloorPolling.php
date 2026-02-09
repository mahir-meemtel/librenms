<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessNoiseFloorPolling
{
    /**
     * Poll wireless noise floor
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessNoiseFloor(array $sensors);
}
