<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessErrorRatioPolling
{
    /**
     * Poll wireless bit/packet error ratio as percent
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessErrorRatio(array $sensors);
}
