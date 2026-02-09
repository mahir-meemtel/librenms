<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrpDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrqDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSinrDiscovery;
use ObzoraNMS\OS;

class IcrOs extends OS implements
    WirelessRssiDiscovery,
    WirelessRsrpDiscovery,
    WirelessRsrqDiscovery,
    WirelessSinrDiscovery
{
    private function runWirelessSensor(string $miboid, string $what, array $nums): array
    {
        $sensors = [];
        foreach ($nums as $index => $num) {
            $oid = "$miboid.$num.0";
            $mobile = $index + 1;
            $name = "Mobile $mobile $what";
            $sensors[] = new WirelessSensor(
                strtolower($what),
                $this->getDeviceId(),
                $oid,
                'icr-os',
                $index,
                $name
            );
        }

        return $sensors;
    }

    public function discoverWirelessRssi()
    {
        $miboid = '.1.3.6.1.4.1.30140.4';
        $what = 'RSSI';
        $nums = [30, 130];

        return $this->runWirelessSensor($miboid, $what, $nums);
    }

    public function discoverWirelessRsrp()
    {
        $miboid = '.1.3.6.1.4.1.30140.4';
        $what = 'RSRP';
        $nums = [32, 132];

        return $this->runWirelessSensor($miboid, $what, $nums);
    }

    public function discoverWirelessRsrq()
    {
        $miboid = '.1.3.6.1.4.1.30140.4';
        $what = 'RSRQ';
        $nums = [33, 133];

        return $this->runWirelessSensor($miboid, $what, $nums);
    }

    public function discoverWirelessSinr()
    {
        $miboid = '.1.3.6.1.4.1.30140.4';
        $what = 'SINR';
        $nums = [41, 141];

        return $this->runWirelessSensor($miboid, $what, $nums);
    }
}
