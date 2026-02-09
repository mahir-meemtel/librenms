<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessErrorsPolling
{
    /**
     * Poll wireless bit errors as total bits
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessErrors(array $sensors);
}
