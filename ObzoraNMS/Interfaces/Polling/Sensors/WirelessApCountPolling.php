<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessApCountPolling
{
    /**
     * Poll wireless AP count
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessApCount(array $sensors);
}
