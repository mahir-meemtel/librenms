<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessErrorsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Aprisa extends OS implements
    WirelessPowerDiscovery,
    WirelessRssiDiscovery,
    WirelessSnrDiscovery,
    WirelessErrorsDiscovery,
    WirelessFrequencyDiscovery
{
    /**
     * Discover wireless tx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        $oid = '.1.3.6.1.4.1.14817.7.3.1.2.36.8.0';

        return [
            new WirelessSensor('power', $this->getDeviceId(), $oid, 'radio', 1, 'TX Power'),
        ];
    }

    /**
     * Discover wireless rx rssi. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        $oid = '.1.3.6.1.4.1.14817.7.3.1.2.51.6.0';

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'radio', 1, 'RX Power', null, 1, 10),
        ];
    }

    /**
     * Discover wireless SNR.  This is in dB. Type is snr.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSnr()
    {
        $oid = '.1.3.6.1.4.1.14817.7.3.1.2.6.3.0';

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'radio', 1, 'SNR', null, 1, 100),
        ];
    }

    /**
     * Discover wireless bit errors.  This is in total bits. Type is errors.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessErrors()
    {
        $oidcorrectable = '.1.3.6.1.4.1.14817.7.3.1.2.6.1.0';
        $oiduncorrectable = '.1.3.6.1.4.1.14817.7.3.1.2.6.2.0';

        return [
            new WirelessSensor('errors', $this->getDeviceId(), $oidcorrectable, 'radio', 1, 'Correctable Errors'),
            new WirelessSensor('errors', $this->getDeviceId(), $oiduncorrectable, 'radio', 2, 'Uncorrectable Errors'),
        ];
    }

    /**
     * Discover wireless frequency.  This is in MHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        $oidrx = '.1.3.6.1.4.1.14817.7.3.1.2.51.5.0';
        $oidtx = '.1.3.6.1.4.1.14817.7.3.1.2.36.7.0';

        return [
            new WirelessSensor('frequency', $this->getDeviceId(), $oidrx, 'radio', 'rx', 'Rx Frequency', null, 1, 1000000),
            new WirelessSensor('frequency', $this->getDeviceId(), $oidtx, 'radio', 'tx', 'Tx Frequency', null, 1, 1000000),
        ];
    }
}
