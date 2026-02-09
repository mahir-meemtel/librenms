<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Ericsson6600 extends OS implements
    WirelessFrequencyDiscovery,
    WirelessPowerDiscovery,
    WirelessRateDiscovery,
    WirelessSnrDiscovery
{
    public function discoverWirelessSnr()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'xfCarrierTermSNIR', [], 'XF-RADIOLINK-RLT-MIB');
        $carrier = $this->getCacheTable('xfCarrierTermDistinguishedName', 'XF-RADIOLINK-RLT-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'snr',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.193.81.3.4.5.1.3.1.17.' . $index,
                'ericsson-6600',
                $index,
                'SNR: ' . $carrier[$index]['xfCarrierTermDistinguishedName'],
                null,
                1,
                10
            );
        }

        return $sensors;
    }

    public function discoverWirelessRate()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'xfCarrierTermActualCapacity', [], 'XF-RADIOLINK-RLT-MIB');
        $carrier = $this->getCacheTable('xfCarrierTermDistinguishedName', 'XF-RADIOLINK-RLT-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rate',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.193.81.3.4.5.1.3.1.13.' . $index,
                'ericsson-6600',
                $index,
                'Rate: ' . $carrier[$index]['xfCarrierTermDistinguishedName'],
                null,
                1000,
                1
            );
        }

        return $sensors;
    }

    public function discoverWirelessFrequency()
    {
        $sensors = [];

        $data_tx = snmpwalk_cache_oid($this->getDeviceArray(), 'xfRFBaseTxFrequency', [], 'XF-RADIOLINK-PTP-RADIO-MIB');
        $data_rx = snmpwalk_cache_oid($this->getDeviceArray(), 'xfRFBaseRxFrequency', [], 'XF-RADIOLINK-PTP-RADIO-MIB');
        $ifname = $this->getCacheTable('ifName', 'IF-MIB');
        foreach ($data_tx as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.193.81.3.4.3.1.2.1.1.' . $index,
                'ericsson-6600',
                $index . 'tx',
                'TX Frequency: ' . $ifname[$index]['ifName'],
                null,
                1,
                1000
            );
        }
        foreach ($data_rx as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'frequency',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.193.81.3.4.3.1.2.1.2.' . $index,
                'ericsson-6600',
                $index . 'rx',
                'RX Frequency: ' . $ifname[$index]['ifName'],
                null,
                1,
                1000
            );
        }

        return $sensors;
    }

    public function discoverWirelessPower()
    {
        $sensors = [];

        $data_tx = snmpwalk_cache_oid($this->getDeviceArray(), 'xfRfCurrentOutputPower', [], 'XF-RADIOLINK-PTP-RADIO-MIB');
        $data_rx = snmpwalk_cache_oid($this->getDeviceArray(), 'xfRfCurrentInputPower', [], 'XF-RADIOLINK-PTP-RADIO-MIB');
        $ifname = $this->getCacheTable('ifName', 'IF-MIB');
        foreach ($data_tx as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'power',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.193.81.3.4.3.1.8.1.3.' . $index,
                'ericsson-6600',
                $index . 'tx',
                'Output power: ' . $ifname[$index]['ifName'],
            );
        }
        foreach ($data_rx as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'power',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.193.81.3.4.3.1.8.1.7.' . $index,
                'ericsson-6600',
                $index . 'rx',
                'Input power: ' . $ifname[$index]['ifName'],
                null,
                1,
                10

            );
        }

        return $sensors;
    }
}
