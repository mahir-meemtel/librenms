<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrpDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrqDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSinrDiscovery;
use ObzoraNMS\OS\Shared\Fortinet;

class Fortiextender extends Fortinet implements
    WirelessSinrDiscovery,
    WirelessRsrpDiscovery,
    WirelessRsrqDiscovery,
    WirelessRssiDiscovery
{
    public function discoverWirelessSinr()
    {
        $sinr_group = snmpwalk_group($this->getDeviceArray(), 'fextInfoModemStatusSINR', 'FORTINET-FORTIEXTENDER-MIB', 1);
        $oid = '.1.3.6.1.4.1.12356.121.21.3.1.1.28.';

        $sinr = [];
        foreach ($sinr_group as $key => $sinr_entry) {
            $sinr[] = new WirelessSensor('sinr', $this->getDeviceId(), $oid . $key, 'fortiextender', $key, 'Modem ' . $key);
        }

        return $sinr;
    }

    public function discoverWirelessRsrp()
    {
        $rsrp_group = snmpwalk_group($this->getDeviceArray(), 'fextInfoModemStatusSINR', 'FORTINET-FORTIEXTENDER-MIB', 1);
        $oid = '.1.3.6.1.4.1.12356.121.21.3.1.1.29.';

        $rsrp = [];
        foreach ($rsrp_group as $key => $rsrp_entry) {
            $rsrp[] = new WirelessSensor('rsrp', $this->getDeviceId(), $oid . $key, 'fortiextender', $key, 'Modem ' . $key);
        }

        return $rsrp;
    }

    public function discoverWirelessRsrq()
    {
        $rsrq_group = snmpwalk_group($this->getDeviceArray(), 'fextInfoModemStatusRSRQ', 'FORTINET-FORTIEXTENDER-MIB', 1);
        $oid = '.1.3.6.1.4.1.12356.121.21.3.1.1.30.';

        $rsrq = [];
        foreach ($rsrq_group as $key => $rsrq_entry) {
            $rsrq[] = new WirelessSensor('rsrq', $this->getDeviceId(), $oid . $key, 'fortiextender', $key, 'Modem ' . $key);
        }

        return $rsrq;
    }

    public function discoverWirelessRssi()
    {
        $rsrq_group = snmpwalk_group($this->getDeviceArray(), 'fextInfoModemStatusRSSI', 'FORTINET-FORTIEXTENDER-MIB', 1);
        $oid = '.1.3.6.1.4.1.12356.121.21.3.1.1.22.';

        $rsrq = [];
        foreach ($rsrq_group as $key => $rsrq_entry) {
            $rsrq[] = new WirelessSensor('rssi', $this->getDeviceId(), $oid . $key, 'fortiextender', $key, 'Modem ' . $key);
        }

        return $rsrq;
    }
}
