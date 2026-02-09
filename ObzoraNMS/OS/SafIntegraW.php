<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessMseDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessQualityDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\OS;

class SafIntegraW extends OS implements
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
            // SAF-INTEGRAW-MIB::integraWradioTxFrequency
            new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.2.2.0',
                'saf-integra-tx',
                'integraWradioTxFrequency',
                'Tx Frequency',
                null,
                1,
                1000
            ),
            // SAF-INTEGRAW-MIB::integraWradioRxFrequency
            new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.2.7.0',
                'saf-integra-rx',
                'integraWradioRxFrequency',
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
            // SAF-INTEGRAW-MIB::integraWmodemMse
            new WirelessSensor(
                'mse',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.3.2.0',
                'saf-integraw-modem',
                'integraWmodemMse',
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
            // SAF-INTEGRAW-MIB::integraWradioTxPower
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.2.1.0',
                'saf-integra-tx',
                'integraWradioTxPower',
                'Tx Power'
            ),
            // SAF-INTEGRAW-MIB::integraWradioRxLevel
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.2.3.0',
                'saf-integra-rx-level',
                'integraWradioRxLevel',
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
            // SAF-INTEGRAW-MIB::integraWmodemRxCapacity
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.3.10.0',
                'saf-integra-rx',
                'integraWmodemRxCapacity',
                'RX Capacity',
                null,
                1000
            ),
            // SAF-INTEGRAW-MIB::integraWmodemTxCapacity
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.3.11.0',
                'saf-integra-tx',
                'integraWmodemTxCapacity',
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
            // SAF-INTEGRAW-MIB::integraWmodemSignalQuality
            new WirelessSensor(
                'quality',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.7571.100.1.1.7.2.3.14.0',
                'saf-integra-quality',
                'integraWmodemSignalQuality',
                'Model Signal',
                null,
                1
            ),
        ];
    }
}
