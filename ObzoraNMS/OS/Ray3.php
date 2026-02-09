<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Ray3 extends OS implements
    ProcessorDiscovery,
    WirelessFrequencyDiscovery,
    WirelessPowerDiscovery,
    WirelessRssiDiscovery,
    WirelessRateDiscovery,
    WirelessSnrDiscovery
{
    /**
     * Discover wireless frequency.  This is in GHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        return [
            // RAY3-MIB::txFreq.0
            new WirelessSensor('frequency', $this->getDeviceId(), '.1.3.6.1.4.1.33555.4.2.1.4.0', 'racom3-tx', 1, 'TX Frequency', null, 1, 1000),
            // RAY3-MIB::rxFreq.0
            new WirelessSensor('frequency', $this->getDeviceId(), '.1.3.6.1.4.1.33555.4.2.1.3.0', 'racom3-rx', 1, 'RX Frequency', null, 1, 1000),
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
            // RAY3-MIB::rfPowerCurrent.0
            new WirelessSensor('power', $this->getDeviceId(), '.1.3.6.1.4.1.33555.4.2.1.17.0', 'racom3-pow-cur', 1, 'Tx Power Current'),
            //RAY3-MIB::rfPowerConfigured.0
            new WirelessSensor('power', $this->getDeviceId(), '.1.3.6.1.4.1.33555.4.2.1.12.0', 'racom3-pow-conf', 1, 'Tx Power Configured'),
        ];
    }

    /**
     * Discover wireless RSSI (Received Signal Strength Indicator). This is in dBm. Type is rssi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        $oid = '.1.3.6.1.4.1.33555.4.3.2.1.0'; // RAY3-MIB::rss.0

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'racom3', 1, 'RSSI', null, 1, 10),
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
        $oid = '.1.3.6.1.4.1.33555.4.3.2.2.0'; // RAY3-MIB::snr.0

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'racom3', 1, 'CINR', null, 1, 10),
        ];
    }

    /**
     * Discover wireless RATE.  This is in bps. Type is rate.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessRate()
    {
        $oid_bitrate = '.1.3.6.1.4.1.33555.4.2.1.13.0'; // RAY3-MIB::netBitrate.0
        $oid_maxbitrate = '.1.3.6.1.4.1.33555.4.2.1.14.0'; // RAY3-MIB::maxNetBitrate.0

        return [
            new WirelessSensor('rate', $this->getDeviceId(), $oid_bitrate, 'racom3-netBitrate', 1, 'Net Bitrate', null, 1000, 1),
            new WirelessSensor('rate', $this->getDeviceId(), $oid_maxbitrate, 'racom3-maxNetBitrate', 2, 'Max Net Bitrate', null, 1000000, 1),
        ];
    }
}
