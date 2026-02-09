<?php
namespace ObzoraNMS\OS\Traits;

use ObzoraNMS\Device\WirelessSensor;

trait CiscoCellular
{
    public function profileApn($index)
    {
        $cwceLteProfileApn = snmpwalk_cache_oid($this->getDeviceArray(), 'cwceLteProfileApn', [], 'CISCO-WAN-CELL-EXT-MIB');
        $device = snmp_get($this->getDeviceArray(), 'entPhysicalName.' . $index, '-Oqv', 'ENTITY-MIB');
        $device = ($device == '' ? strval($index) : preg_replace('/Modem(.*)Cellular/', 'Ce', $device));
        $apn = $cwceLteProfileApn[$index . '.1']['cwceLteProfileApn'];
        if ($apn == '') {
            return $device;
        }

        return $device . ' ' . $apn;
    }

    /**
     * Discover wireless RSSI (Received Signal Strength Indicator). This is in dBm. Type is rssi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'c3gCurrentGsmRssi', [], 'CISCO-WAN-3G-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rssi',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.9.9.661.1.3.4.1.1.1.' . $index,
                'ios',
                $index,
                'RSSI: ' . $this->profileApn($index),
                $entry['c3gCurrentGsmRssi']
            );
        }

        return $sensors;
    }

    /**
     * Discover wireless SNR (Signal-to-Noise Ratio). This is in dB. Type is snr.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessSnr()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'cwceLteCurrSnr', [], 'CISCO-WAN-CELL-EXT-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'snr',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.9.9.817.1.1.1.1.1.3.' . $index,
                'ios',
                $index,
                'SNR: ' . $this->profileApn($index),
                $entry['cwceLteCurrSnr'],
                1,
                10
            );
        }

        return $sensors;
    }

    /**
     * Discover wireless RSRQ (Reference Signal Received Quality). This is in dB. Type is rsrq.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRsrq()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'cwceLteCurrRsrq', [], 'CISCO-WAN-CELL-EXT-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rsrq',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.9.9.817.1.1.1.1.1.2.' . $index,
                'ios',
                $index,
                'RSRQ: ' . $this->profileApn($index),
                $entry['cwceLteCurrRsrq'],
                1,
                10
            );
        }

        return $sensors;
    }

    /**
     * Discover wireless RSRP (Reference Signals Received Power). This is in dBm. Type is rsrp.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRsrp()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'cwceLteCurrRsrp', [], 'CISCO-WAN-CELL-EXT-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rsrp',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.9.9.817.1.1.1.1.1.1.' . $index,
                'ios',
                $index,
                'RSRP: ' . $this->profileApn($index),
                $entry['cwceLteCurrRsrp']
            );
        }

        return $sensors;
    }

    /**
     * Discover wireless SINR (Signal-to-Interference-plus-Noise Ratio). This is in dB. Type is sinr.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessChannel()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'c3gGsmChannelNumber', [], 'CISCO-WAN-3G-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'channel',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.9.9.661.1.3.4.1.1.4.' . $index,
                'ios',
                $index,
                'ChannelRx: ' . $this->profileApn($index),
                $entry['c3gGsmChannelNumber']
            );
        }

        return $sensors;
    }

    /**
     * Discover wireless Cellular Cell Id. This is in cell number. Type is cellid.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessCell()
    {
        $sensors = [];

        $data = snmpwalk_cache_oid($this->getDeviceArray(), 'c3gGsmCurrentCellId', [], 'CISCO-WAN-3G-MIB');
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'cell',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.9.9.661.1.3.2.1.13.' . $index,
                'ios',
                $index,
                'Cell: ' . $this->profileApn($index),
                $entry['c3gGsmCurrentCellId']
            );
        }

        return $sensors;
    }
}
