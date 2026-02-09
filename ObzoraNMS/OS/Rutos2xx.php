<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;

class Rutos2xx extends OS implements
    OSPolling,
    WirelessSnrDiscovery,
    WirelessRssiDiscovery
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        // Mobile Data Usage
        $usage = snmp_get_multi_oid($this->getDeviceArray(), [
            '.1.3.6.1.4.1.48690.2.11.0',
            '.1.3.6.1.4.1.48690.2.10.0',
        ]);

        $usage_sent = $usage['.1.3.6.1.4.1.48690.2.11.0'];
        $usage_received = $usage['.1.3.6.1.4.1.48690.2.10.0'];

        if ($usage_sent >= 0 && $usage_received >= 0) {
            $rrd_def = RrdDefinition::make()
                ->addDataset('usage_sent', 'GAUGE', 0)
                ->addDataset('usage_received', 'GAUGE', 0);

            $fields = [
                'usage_sent' => $usage_sent,
                'usage_received' => $usage_received,
            ];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'rutos_2xx_mobileDataUsage', $tags, $fields);
            $this->enableGraph('rutos_2xx_mobileDataUsage');
        }
    }

    public function discoverWirelessSnr()
    {
        $oid = '.1.3.6.1.4.1.48690.2.22.0'; // TELTONIKA-MIB::SINR.0

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'rutos-2xx', 1, 'SINR', null, -1, 1),
        ];
    }

    public function discoverWirelessRssi()
    {
        $oid = '.1.3.6.1.4.1.48690.2.23.0'; // TELTONIKA-MIB::RSRP.0

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'rutos-2xx', 1, 'RSRP', null, 1, 1),
        ];
    }
}
