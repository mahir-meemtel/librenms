<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessMseDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessQualityDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\OS;

class SafIntegraE extends OS implements
    WirelessFrequencyDiscovery,
    WirelessMseDiscovery,
    WirelessPowerDiscovery,
    WirelessRateDiscovery,
    WirelessQualityDiscovery
{
    /**
     * Discover wireless frequency.  This is in MHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        return [
            // SAF-INTEGRAE-MIB::integraEradioTxFrequency
            new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.2.2.0',
                'saf-integra-tx',
                'integraEradioTxFrequency',
                'Tx Frequency',
                null,
                1,
                1000
            ),
            // SAF-INTEGRAE-MIB::integraEradioRxFrequency
            new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.2.7.0',
                'saf-integra-rx',
                'integraEradioRxFrequency',
                'Rx Frequency',
                null,
                1,
                1000
            ),
        ];
    }

    /**
     * Discover wireless MSE. Mean square error value *10 in dB.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessMse()
    {
        return [
            // SAF-INTEGRAE-MIB::integraEmodemMse
            new WirelessSensor(
                'mse',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.3.2.0',
                'saf-integrae-modem',
                'integraEmodemMse',
                'Modem MSE',
                null,
                1,
                10
            ),
        ];
    }

    /**
     * Discover wireless tx or rx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        return [
            // SAF-INTEGRAE-MIB::integraEradioTxPower
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.2.1.0',
                'saf-integra-tx',
                'integraEradioTxPower',
                'Tx Power'
            ),
            // SAF-INTEGRAE-MIB::integraEradioRxLevel
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.2.3.0',
                'saf-integra-rx-level',
                'integraEradioRxLevel',
                'Rx Level'
            ),
        ];
    }

    /**
     * Discover wireless rate. This is in bps. Type is rate.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRate()
    {
        return [
            // SAF-INTEGRAE-MIB::integraEmodemRxCapacity
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.3.10.0',
                'saf-integra-rx',
                'integraEmodemRxCapacity',
                'RX Capacity',
                null,
                1000
            ),
            // SAF-INTEGRAE-MIB::integraEmodemTxCapacity
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.3.11.0',
                'saf-integra-tx',
                'integraEmodemTxCapacity',
                'TX Capacity',
                null,
                1000
            ),
        ];
    }

    /**
     * Discover wireless quality.  This is a percent. Type is quality.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessQuality()
    {
        return [
            // SAF-INTEGRAE-MIB::integraEmodemSignalQuality
            new WirelessSensor(
                'quality',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.9.3.14.0',
                'saf-integra-quality',
                'integraEmodemSignalQuality',
                'Model Signal',
                null,
                1
            ),
        ];
    }
}
