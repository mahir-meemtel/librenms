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

class EricssonMl extends OS implements
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
            // PT-RADIOLINK-MIB::txFrequency.0
            new WirelessSensor('frequency', $this->getDeviceId(), '.1.3.6.1.4.1.193.223.2.7.1.1.10.110101', 'eric-tx', 1, 'TX Frequency', null, 1, 1000),
            // PT-RADIOLINK-MIB::rxFrequency.0
            new WirelessSensor('frequency', $this->getDeviceId(), '.1.3.6.1.4.1.193.223.2.7.1.1.13.110101', 'eric-rx', 1, 'RX Frequency', null, 1, 1000),
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
            // PT-RADIOLINK-MIB::actualOutputPower.0
            new WirelessSensor('power', $this->getDeviceId(), '.1.3.6.1.4.1.193.223.2.7.1.1.2.110101', 'eric-pow-cur', 1, 'Tx Power Current'),
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
        $oid = '.1.3.6.1.4.1.193.223.2.7.1.1.1.110101'; // PT-RADIOLINK-MIB::actualInputPower.0

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'eric', 1, 'RSSI', null, 1, 1),
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
        $oid = '.1.3.6.1.4.1.193.223.2.7.1.1.43.110101'; //PT-RADIOLINK-MIB::actualSnir.0

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'eric', 1, 'CINR', null, 1, 1),
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
        $oid_bitrate = '.1.3.6.1.4.1.193.223.2.7.1.1.46.110101'; // PT-RADIOLINK-MIB::actualTxCapacity.0

        return [
            new WirelessSensor('rate', $this->getDeviceId(), $oid_bitrate, 'eric-netBitrate', 1, 'Net Bitrate', null, 1000, 1),
        ];
    }
}
