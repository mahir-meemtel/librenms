<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessApCountDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\OS\Shared\Zyxel;

class Zyxelwlc extends Zyxel implements WirelessApCountDiscovery, WirelessClientsDiscovery
{
    public function discoverWirelessClients()
    {
        $oid = '.1.3.6.1.4.1.890.1.15.3.3.1.4.0'; //    ZYXEL-ES-CAPWAP::capwapTotalStation
        $total_station = (int) snmp_get($this->getDeviceArray(), '.1.3.6.1.4.1.890.1.15.3.3.1.4.0', '-Ovq'); //    ZYXEL-ES-CAPWAP::capwapTotalStation

        $sensors[] = new WirelessSensor(
            'clients',
            $this->getDeviceId(),
            $oid,
            'zyxelwlc',
            0,
            'Clients: Total',
            $total_station
        );

        return $sensors;
    }

    public function discoverWirelessApCount()
    {
        $oid = '.1.3.6.1.4.1.890.1.15.3.3.1.1.0'; //  ZYXEL-ES-CAPWAP::capwapOnlineAP
        $number_ap = (int) snmp_get($this->getDeviceArray(), '.11.3.6.1.4.1.890.1.15.3.3.1.1.0', '-Ovq'); // ZYXEL-ES-CAPWAP::capwapOnlineAP

        if ($this->getDeviceArray()['hardware'] == 'NXC2500') {
            $max_ap = 64;
        } elseif ($this->getDeviceArray()['hardware'] == 'NXC5200') {
            $max_ap = 240;
        } elseif ($this->getDeviceArray()['hardware'] == 'NXC5500') {
            $max_ap = 1024;
        } else {
            $max_ap = 0;
        }

        return [
            new WirelessSensor(
                'ap-count',
                $this->getDeviceId(),
                $oid,
                'zyxelwlc',
                0,
                'Connected APs',
                $number_ap,
                1,
                1,
                'sum',
                null,
                $max_ap,
                0
            ),
        ];
    }
}
