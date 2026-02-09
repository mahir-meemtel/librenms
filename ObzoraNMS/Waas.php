<?php
namespace ObzoraNMS;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\RRD\RrdDefinition;

class Waas extends OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $connections = \SnmpQuery::get('CISCO-WAN-OPTIMIZATION-MIB::cwoTfoStatsActiveOptConn.0')->value();

        if (is_numeric($connections)) {
            $datastore->put($this->getDeviceArray(), 'waas_cwotfostatsactiveoptconn', [
                'rrd_def' => RrdDefinition::make()->addDataset('connections', 'GAUGE', 0),
            ], [
                'connections' => $connections,
            ]);
            $this->enableGraph('waas_cwotfostatsactiveoptconn');
        }
    }
}
