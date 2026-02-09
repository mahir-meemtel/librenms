<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessDistanceDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\OS;

class Radwin extends OS implements
    WirelessDistanceDiscovery,
    WirelessPowerDiscovery,
    WirelessRssiDiscovery
{
    /**
     * Discover wireless distance.  This is in Kilometers. Type is distance.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessDistance()
    {
        $oid = '.1.3.6.1.4.1.4458.1000.1.5.29.0'; //RADWIN-MIB-WINLINK1000::winlink1000OduAirLinkDistance.0

        return [
            new WirelessSensor('distance', $this->getDeviceId(), $oid, 'radwin', 0, 'Link distance', null, 1, 1000),
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
        $transmit = '.1.3.6.1.4.1.4458.1000.1.5.4.0'; //RADWIN-MIB-WINLINK1000::winlink1000OduAirTxPower.0
        $receive = '.1.3.6.1.4.1.4458.1000.1.5.9.1.0'; //RADWIN-MIB-WINLINK1000::winlink1000OduAirRxPower.0

        return [
            new WirelessSensor('power', $this->getDeviceId(), $transmit, 'Radwin-Tx', 0, 'Tx Power'),
            new WirelessSensor('power', $this->getDeviceId(), $receive, 'Radwin-Rx', 0, 'Rx Power'),
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
        $oid = '.1.3.6.1.4.1.4458.1000.1.1.51.7.0'; // RADWIN-MIB-WINLINK1000::winlink1000OduAdmWifiRssi.0

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'radwin', 0, 'RSSI'),
        ];
    }
}
