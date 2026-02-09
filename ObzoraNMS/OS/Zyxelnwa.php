<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Polling\Sensors\WirelessFrequencyPolling;
use ObzoraNMS\OS\Shared\Zyxel;

class Zyxelnwa extends Zyxel implements OSDiscovery, WirelessClientsDiscovery, WirelessFrequencyDiscovery, WirelessFrequencyPolling
{
    public function discoverWirelessClients()
    {
        $sensors = [];
        $base_oid = '.1.3.6.1.4.1.890.1.15.3.5.1.1.2.'; // ZYXEL-ES-WIRELESS::wlanStationCount

        foreach ($this->getWlanRadioTable() as $index => $row) {
            $radio = $this->getRadioName($row['ZYXEL-ES-WIRELESS::wlanMode']);
            $sensors[] = new WirelessSensor('clients', $this->getDeviceId(), $base_oid . $index, 'zyxelnwa', $index, $radio, $row['ZYXEL-ES-WIRELESS::wlanStationCount']);
        }

        $total = \SnmpQuery::options(['-OQXUte', '-Pu'])->get('ZYXEL-ES-WIRELESS::wlanTotalStationCount.0')->value();
        if ($total !== '') {
            $sensors[] = new WirelessSensor('clients', $this->getDeviceId(), '.1.3.6.1.4.1.890.1.15.3.5.15.0', 'zyxelnwa', 'total', 'Total', (int) $total);
        }

        return $sensors;
    }

    public function discoverWirelessFrequency()
    {
        $sensors = [];
        $base_oid = '.1.3.6.1.4.1.890.1.15.3.5.1.1.6.'; // ZYXEL-ES-WIRELESS::wlanChannel

        foreach ($this->getWlanRadioTable() as $index => $row) {
            $radio = $this->getRadioName($row['ZYXEL-ES-WIRELESS::wlanMode']);
            $frequency = WirelessSensor::channelToFrequency($row['ZYXEL-ES-WIRELESS::wlanChannel']);
            $sensors[] = new WirelessSensor('frequency', $this->getDeviceId(), $base_oid . $index, 'zyxelnwa', $index, $radio, $frequency);
        }

        return $sensors;
    }

    public function pollWirelessFrequency(array $sensors)
    {
        return $this->pollWirelessChannelAsFrequency($sensors);
    }

    private function getRadioName($value): string
    {
        return match ($value) {
            '1' => '2.4GHz',
            '2' => '5GHz',
            '3' => '6GHz',
            default => 'Unknown',
        };
    }

    private function getWlanRadioTable()
    {
        return \SnmpQuery::options(['-OQXUte', '-Pu']) // ignore underscores
            ->cache()
            ->walk('ZYXEL-ES-WIRELESS::wlanRadioTable')
            ->table(1);
    }
}
