<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\OS;

class Ptp800 extends OS implements
    WirelessPowerDiscovery,
    WirelessRateDiscovery
{
    /**
     * Discover wireless tx or rx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        $transmit = '.1.3.6.1.4.1.17713.8.12.3.0'; //"CAMBIUM-PTP800-MIB::transmitPower.0"
        $receive = '.1.3.6.1.4.1.17713.8.12.1.0'; //"CAMBIUM-PTP800-MIB::receivePower.0";

        return [
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                $transmit,
                'ptp800-tx',
                0,
                'PTP800 Transmit',
                null,
                1,
                10
            ),
            new WirelessSensor(
                'power',
                $this->getDeviceId(),
                $receive,
                'ptp800-rx',
                0,
                'PTP800 Receive',
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
        $receive = '.1.3.6.1.4.1.17713.8.20.1.0'; //"CAMBIUM-PTP800-MIB::receiveDataRate.0"
        $transmit = '.1.3.6.1.4.1.17713.8.20.2.0'; //"CAMBIUM-PTP800-MIB::transmitDataRate.0"
        $aggregate = '.1.3.6.1.4.1.17713.8.20.3.0'; //"CAMBIUM-PTP800-MIB::aggregateDataRate.0"

        return [
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $receive,
                'ptp800-rx-rate',
                0,
                'PTP800 Receive Rate',
                null,
                1000,
                1
            ),
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $transmit,
                'ptp800-tx-rate',
                0,
                'PTP800 Transmit Rate',
                null,
                1000,
                1
            ),
            new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                $aggregate,
                'ptp800-ag-rate',
                0,
                'PTP800 Aggregate Rate',
                null,
                1000,
                1
            ),
        ];
    }
}
