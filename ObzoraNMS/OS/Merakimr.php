<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Polling\Sensors\WirelessFrequencyPolling;
use ObzoraNMS\OS;

class Merakimr extends OS implements
    WirelessFrequencyDiscovery,
    WirelessFrequencyPolling
{
    public function discoverWirelessFrequency()
    {
        $mrRadioChannelOper = $this->getCacheByIndex('dot11CurrentChannel', 'IEEE802dot11-MIB');
        $sensors = [];
        $lastChannel = null;

        foreach ($mrRadioChannelOper as $index => $channel) {
            if ($lastChannel != $channel) {
                $sensors[] = new WirelessSensor(
                    'frequency',
                    $this->getDeviceId(),
                    '.1.2.840.10036.4.5.1.1.' . $index,
                    'merakimr',
                    'Radio ' . $index,
                    "Frequency (Radio $index)",
                    WirelessSensor::channelToFrequency($channel)
                );
            }
            $lastChannel = $channel;
        }

        return $sensors;
    }

    public function pollWirelessFrequency(array $sensors)
    {
        return $this->pollWirelessChannelAsFrequency($sensors);
    }
}
