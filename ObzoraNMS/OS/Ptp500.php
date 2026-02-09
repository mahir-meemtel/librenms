<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSsrDiscovery;
use ObzoraNMS\OS;

class Ptp500 extends OS implements
    WirelessPowerDiscovery,
    WirelessRateDiscovery,
    WirelessSsrDiscovery
{
    /**
     * Discover wireless tx or rx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        $transmit = '.1.3.6.1.4.1.17713.5.12.3.0'; //"CAMBIUM-PTP500-V2-MIB::transmitPower.0"
        $receive = '.1.3.6.1.4.1.17713.5.12.1.0'; //"CAMBIUM-PTP500-V2-MIB::receivePower.0";

        return [
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                $transmit,
                'ptp500-tx',
                0,
                'PTP500 Transmit',
                null,
                1,
                10
            ),
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                $receive,
                'ptp500-rx',
                0,
                'PTP500 Receive',
                null,
                1,
                10
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
        $receive = '.1.3.6.1.4.1.17713.5.20.1.0'; //"CAMBIUM-PTP500-V2-MIB::receiveDataRate.0"
        $transmit = '.1.3.6.1.4.1.17713.5.20.2.0'; //"CAMBIUM-PTP500-V2-MIB::transmitDataRate.0"
        $aggregate = '.1.3.6.1.4.1.17713.5.20.3.0'; //"CAMBIUM-PTP500-V2-MIB::aggregateDataRate.0"

        return [
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $receive,
                'ptp500-rx-rate',
                0,
                'PTP500 Receive Rate',
                null,
                1000,
                1
            ),
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $transmit,
                'ptp500-tx-rate',
                0,
                'PTP500 Transmit Rate',
                null,
                1000,
                1
            ),
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $aggregate,
                'ptp500-ag-rate',
                0,
                'PTP500 Aggregate Rate',
                null,
                1000,
                1
            ),
        ];
    }

    /**
     * Discover wireless SSR.  This is in dB. Type is ssr.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSsr()
    {
        $ssr = '.1.3.6.1.4.1.17713.5.12.13.0'; // CAMBIUM-PTP500-V2-MIB::signalStrengthRatio.0

        return [
            new WirelessSensor(
                'ssr',
                $this->getDeviceId(),
                $ssr,
                'ptp500-ssr',
                0,
                'PTP500 Signal Strength Ratio',
                null,
                1,
                10
            ),
        ];
    }
}
