<?php
namespace ObzoraNMS\Interfaces\Polling\Sensors;

interface WirelessChannelPolling
{
    /**
     * Poll Wireless Channel.  Type is channel.
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessChannel(array $sensors);
}
