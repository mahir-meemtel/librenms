<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessRsrpPolling
{
    /**
     * Poll wireless RSRP (Reference Signal Received Power) in dBm
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessRsrp(array $sensors);
}
