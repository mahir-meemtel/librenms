<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\RRD\RrdDefinition;

class Secureplatform extends \ObzoraNMS\OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $connections = snmp_get($this->getDeviceArray(), 'fwNumConn.0', '-OQv', 'CHECKPOINT-MIB');

        if (is_numeric($connections)) {
            $rrd_def = RrdDefinition::make()->addDataset('NumConn', 'GAUGE', 0);

            $fields = [
                'NumConn' => $connections,
            ];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'secureplatform_sessions', $tags, $fields);
            $this->enableGraph('secureplatform_sessions');
        }
    }
}
