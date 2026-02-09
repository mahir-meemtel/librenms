<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessDistanceDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Tachyon extends OS implements
    WirelessClientsDiscovery,
    WirelessFrequencyDiscovery,
    WirelessPowerDiscovery,
    WirelessRssiDiscovery,
    WirelessSnrDiscovery,
    WirelessRateDiscovery,
    WirelessDistanceDiscovery
{
    /**
     * Discover wireless clients.
     */
    public function discoverWirelessClients()
    {
        $sensors = [];
        $oid = '.1.3.6.1.4.1.57344.1.2.5.0';

        $sensors[] = new WirelessSensor(
            'clients',
            $this->getDeviceId(),
            $oid,
            'tachyon-clients',
            1,
            'Clients',
            null,
            1,
            1
        );

        return $sensors;
    }

    /**
     * Discover wireless frequency.
     */
    public function discoverWirelessFrequency()
    {
        $sensors = [];
        $data = $this->getCacheTable('TACHYON-MIB::wirelessRadioTable');

        foreach ($data as $index => $entry) {
            if (! empty($entry['wirelessRadioFrequency'])) {
                $sensors[] = new WirelessSensor(
                    'frequency',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.2.1.5.$index",
                    'tachyon-freq',
                    $index,
                    'Frequency',
                    null,
                    1,
                    1
                );
            }
        }

        return $sensors;
    }

    /**
     * Discover wireless TX power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered.
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        $sensors = [];
        $data = $this->getCacheTable('TACHYON-MIB::wirelessRadioTable');

        foreach ($data as $index => $entry) {
            if (! empty($entry['wirelessRadioTxPower'])) {
                $sensors[] = new WirelessSensor(
                    'power',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.2.1.3.$index",
                    'tachyon-tx-power',
                    $index,
                    'TX Power',
                    null,
                    1,
                    1
                );
            }
        }

        return $sensors;
    }

    /**
     * Discover wireless RX RSSI.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered.
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        $sensors = [];
        $data = $this->getCacheTable('TACHYON-MIB::wirelessPeersTable');

        foreach ($data as $index => $entry) {
            if (! empty($entry['wirelessPeerRxPower'])) {
                $mac = $entry['wirelessPeerMac'];
                $sensors[] = new WirelessSensor(
                    'rssi',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.6.1.6.$index",
                    'tachyon-rx-rssi',
                    $index,
                    "RX ($mac)",
                    null,
                    1,
                    1
                );
            }
        }

        return $sensors;
    }

    /**
     * Discover wireless TX/RX rates.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered.
     *
     * @return array
     */
    public function discoverWirelessRate()
    {
        $sensors = [];
        $data = $this->getCacheTable('TACHYON-MIB::wirelessPeersTable');

        foreach ($data as $index => $entry) {
            if (! empty($entry['wirelessPeerTxRate'])) {
                $mac = $entry['wirelessPeerMac'];
                $sensors[] = new WirelessSensor(
                    'rate',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.6.1.7.$index",
                    'tachyon-tx-rate',
                    $index,
                    "TX ($mac)",
                    null,
                    1000000,
                    1
                );
            }
            if (! empty($entry['wirelessPeerRxRate'])) {
                $mac = $entry['wirelessPeerMac'];
                $sensors[] = new WirelessSensor(
                    'rate',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.6.1.9.$index",
                    'tachyon-rx-rate',
                    $index,
                    "RX ($mac)",
                    null,
                    1000000,
                    1
                );
            }
        }

        return $sensors;
    }

    /**
     * Discover wireless link distance.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered.
     *
     * @return array
     */
    public function discoverWirelessDistance()
    {
        $sensors = [];
        $data = $this->getCacheTable('TACHYON-MIB::wirelessPeersTable');

        foreach ($data as $index => $entry) {
            if (! empty($entry['wirelessPeerLinkDistance'])) {
                $mac = $entry['wirelessPeerMac'];
                $sensors[] = new WirelessSensor(
                    'distance',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.6.1.13.$index",
                    'tachyon-distance',
                    $index,
                    "Sta ($mac)",
                    null,
                    1,
                    1000
                );
            }
        }

        return $sensors;
    }

    /**
     * Discover wireless SNR.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered.
     *
     * @return array
     */
    public function discoverWirelessSnr()
    {
        $sensors = [];
        $data = $this->getCacheTable('TACHYON-MIB::wirelessPeersTable');

        foreach ($data as $index => $entry) {
            if (! empty($entry['wirelessPeerSnr'])) {
                $mac = $entry['wirelessPeerMac'];
                $sensors[] = new WirelessSensor(
                    'snr',
                    $this->getDeviceId(),
                    ".1.3.6.1.4.1.57344.1.2.6.1.8.$index",
                    'tachyon-snr',
                    $index,
                    "SNR ($mac)",
                    null,
                    1,
                    1
                );
            }
        }

        return $sensors;
    }
}
