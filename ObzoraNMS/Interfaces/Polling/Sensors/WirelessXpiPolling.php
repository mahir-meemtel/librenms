<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessXpiPolling
{
    /**
     * Poll wireless Cross Polar Interference.
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessApCount(array $sensors);
}
