<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessErrorRatioDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\OS;

class Extendair extends OS implements
    WirelessErrorRatioDiscovery,
    WirelessFrequencyDiscovery,
    WirelessPowerDiscovery,
    WirelessRateDiscovery
{
    /**
     * Discover wireless bit/packet error ratio.  This is in percent. Type is error-ratio.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessErrorRatio()
    {
        $oid = '.1.3.6.1.4.1.25651.1.2.4.3.1.1.0'; // ExaltComProducts::locCurrentBER.0

        return [
            new WirelessSensor(
                'error-ratio',
                $this->getDeviceId(),
                $oid,
                'extendair',
                0,
                'Bit Error Ratio',
                null,
                1,
                1000000
            ),
        ];
    }

    /**
     * Discover wireless frequency.  This is in GHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        $tx_oid = '.1.3.6.1.4.1.25651.1.2.3.1.57.4.0'; // ExtendAirG2::extendAirG2TXfrequency.0
        $rx_oid = '.1.3.6.1.4.1.25651.1.2.3.1.57.5.0'; // ExtendAirG2::extendAirG2RXfrequency.0

        return [
            new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                $tx_oid,
                'extendair-tx',
                0,
                'Tx Frequency',
                null,
                1,
                1000
            ),
            new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                $rx_oid,
                'extendair-rx',
                0,
                'Rx Frequency',
                null,
                1,
                1000
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
        $tx_oid = '.1.3.6.1.4.1.25651.1.2.3.1.57.1.0'; // ExtendAirG2::extendAirG2TxPower.0
        $rx_oid = '.1.3.6.1.4.1.25651.1.2.4.3.1.3.0'; // ExaltComProducts::locCurrentRSL.0

        return [
            new WirelessSensor('power', $this->getDeviceId(), $tx_oid, 'extendair-tx', 0, 'Tx Power', null, 1, 10),
            new WirelessSensor('power', $this->getDeviceId(), $rx_oid, 'extendair-rx', 0, 'Signal Level'),
        ];
    }

    /**
     * Discover wireless rate. This is in Mbps. Type is rate.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRate()
    {
        $oid = '.1.3.6.1.4.1.25651.1.2.4.5.1.0'; // ExaltComProducts::aggregateUserThroughput.0

        return [
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $oid,
                'extendair',
                0,
                'Aggregate User Throughput',
                null,
                1000000
            ),
        ];
    }
}
