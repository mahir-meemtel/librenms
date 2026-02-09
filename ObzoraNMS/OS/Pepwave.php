<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrpDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrqDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSinrDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Pepwave extends OS implements
    WirelessClientsDiscovery,
    WirelessSnrDiscovery,
    WirelessRsrpDiscovery,
    WirelessRsrqDiscovery,
    WirelessRssiDiscovery,
    WirelessSinrDiscovery
{
    public function discoverWirelessClients()
    {
        $oid = '.1.3.6.1.4.1.27662.4.1.1.7.0';

        return [
            new WirelessSensor('clients', $this->getDeviceId(), $oid, 'pepwave', 1, 'Online APs'),
        ];
    }

    public function discoverWirelessRssi()
    {
        $data = snmpwalk_group($this->getDeviceArray(), 'cellularSignalRssi', 'CELLULAR');
        $sensors = [];
        foreach ($data as $index => $rssi_value) {
            if ($rssi_value['cellularSignalRssi'] != '-9999') {
                $sensors[] = new WirelessSensor('rssi', $this->getDeviceId(), '.1.3.6.1.4.1.23695.200.1.12.1.1.1.3.' . $index, 'pepwave', 'cellularSignalRssi' . $index, 'Celullar ' . ($index + 1), $rssi_value['cellularSignalRssi'], 1, 1);
            }
        }

        return $sensors;
    }

    public function discoverWirelessSnr()
    {
        $data = snmpwalk_group($this->getDeviceArray(), 'cellularSignalSnr', 'CELLULAR');
        $sensors = [];
        foreach ($data as $index => $snr_value) {
            if ($snr_value['cellularSignalSnr'] != '-9999') {
                $sensors[] = new WirelessSensor('snr', $this->getDeviceId(), '.1.3.6.1.4.1.23695.200.1.12.1.1.1.4.' . $index, 'pepwave', 'cellularSignalSnr' . $index, 'Celullar ' . ($index + 1), $snr_value['cellularSignalSnr'], 1, 1);
            }
        }

        return $sensors;
    }

    public function discoverWirelessSinr()
    {
        $data = snmpwalk_group($this->getDeviceArray(), 'cellularSignalSinr', 'CELLULAR');
        $sensors = [];
        foreach ($data as $index => $sinr_value) {
            if ($sinr_value['cellularSignalSinr'] != '-9999') {
                $sensors[] = new WirelessSensor('sinr', $this->getDeviceId(), '.1.3.6.1.4.1.23695.200.1.12.1.1.1.5.' . $index, 'pepwave', 'cellularSignalSinr' . $index, 'Celullar ' . ($index + 1), $sinr_value['cellularSignalSinr'], 1, 1);
            }
        }

        return $sensors;
    }

    public function discoverWirelessRsrp()
    {
        $data = snmpwalk_group($this->getDeviceArray(), 'cellularSignalRsrp', 'CELLULAR');
        $sensors = [];
        foreach ($data as $index => $rsrp_value) {
            if ($rsrp_value['cellularSignalRsrp'] != '-9999') {
                $sensors[] = new WirelessSensor('rsrp', $this->getDeviceId(), '.1.3.6.1.4.1.23695.200.1.12.1.1.1.7.' . $index, 'pepwave', 'cellularSignalRsrp' . $index, 'Celullar ' . ($index + 1), $rsrp_value['cellularSignalRsrp'], 1, 1);
            }
        }

        return $sensors;
    }

    public function discoverWirelessRsrq()
    {
        $data = snmpwalk_group($this->getDeviceArray(), 'cellularSignalRsrq', 'CELLULAR');
        $sensors = [];
        foreach ($data as $index => $rsrq_value) {
            if ($rsrq_value['cellularSignalRsrq'] != '-9999') {
                $sensors[] = new WirelessSensor('rsrq', $this->getDeviceId(), '.1.3.6.1.4.1.23695.200.1.12.1.1.1.8.' . $index, 'pepwave', 'cellularSignalRsrq' . $index, 'Celullar ' . ($index + 1), $rsrq_value['cellularSignalRsrq'], 1, 1);
            }
        }

        return $sensors;
    }
}
