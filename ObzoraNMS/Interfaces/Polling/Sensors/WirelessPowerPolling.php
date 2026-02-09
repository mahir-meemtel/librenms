<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessPowerPolling
{
    /**
     * Poll wireless tx or rx power
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessPower(array $sensors);
}
