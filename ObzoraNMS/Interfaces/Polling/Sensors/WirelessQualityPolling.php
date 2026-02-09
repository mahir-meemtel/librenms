<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessQualityPolling
{
    /**
     * Poll wireless quality as a percent
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessQuality(array $sensors);
}
