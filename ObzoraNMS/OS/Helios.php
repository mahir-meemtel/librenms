<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\OS;

class Helios extends OS implements WirelessFrequencyDiscovery, WirelessPowerDiscovery, WirelessRssiDiscovery
{
    /**
     * Discover wireless frequency.  This is in GHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        return $this->discoverOid('frequency', 'mlRadioInfoFrequency', '.1.3.6.1.4.1.47307.1.4.2.1.4.');
    }

    /**
     * Discover wireless tx or rx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        return $this->discoverOid('power', 'mlRadioInfoTxPower', '.1.3.6.1.4.1.47307.1.4.2.1.7.');
    }

    /**
     * Discover wireless RSSI (Received Signal Strength Indicator). This is in dBm. Type is rssi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        return $this->discoverOid('rssi', 'mlRadioInfoRSSILocal', '.1.3.6.1.4.1.47307.1.4.2.1.10.');
    }

    private function discoverOid($type, $oid, $oid_prefix)
    {
        $oids = snmpwalk_cache_oid($this->getDeviceArray(), $oid, [], 'IGNITENET-MIB');

        $sensors = [];
        foreach ($oids as $index => $data) {
            $sensors[] = new WirelessSensor(
                $type,
                $this->getDeviceId(),
                $oid_prefix . $index,
                'ignitenet',
                $index,
                "Radio $index",
                $data[$oid]
            );
        }

        return $sensors;
    }
}
