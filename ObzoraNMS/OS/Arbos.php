<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;
use SnmpQuery;

class Arbos extends OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $flows = SnmpQuery::get('PEAKFLOW-SP-MIB::deviceTotalFlows.0')->value();

        if (is_numeric($flows)) {
            $datastore->put($this->getDeviceArray(), 'arbos_flows', [
                'rrd_def' => RrdDefinition::make()->addDataset('flows', 'GAUGE', 0, 3000000),
            ], [
                'flows' => $flows,
            ]);

            $this->enableGraph('arbos_flows');
        }
    }
}
