<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessCellDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrpDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrqDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSinrDiscovery;
use ObzoraNMS\OS;

class RutosRutx extends OS implements
    WirelessRssiDiscovery,
    WirelessRsrpDiscovery,
    WirelessRsrqDiscovery,
    WirelessSinrDiscovery,
    WirelessCellDiscovery
{
    public function discoverWirelessRssi(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rssi',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.12.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' RSSI',
                $entry['mSignal']
            );
        }

        return $sensors;
    }

    public function discoverWirelessRsrp(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rsrp',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.20.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' RSRP',
                $entry['mRSRP']
            );
        }

        return $sensors;
    }

    public function discoverWirelessRsrq(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rsrq',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.21.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' RSRQ',
                $entry['mRSRQ']
            );
        }

        return $sensors;
    }

    public function discoverWirelessSinr(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'sinr',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.19.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' SINR',
                $entry['mSINR']
            );
        }

        return $sensors;
    }

    public function discoverWirelessCell(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'cell',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.18.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' CELL ID',
                $entry['CELLID'] ?? null
            );
        }

        return $sensors;
    }
}
