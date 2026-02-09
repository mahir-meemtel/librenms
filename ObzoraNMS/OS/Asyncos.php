<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;

class Asyncos extends OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        // Get stats only if device is web proxy
        if ($this->getDevice()->sysObjectID == '.1.3.6.1.4.1.15497.1.2') {
            $connections = \SnmpQuery::get('TCP-MIB::tcpCurrEstab.0')->value();

            if (is_numeric($connections)) {
                $datastore->put($this->getDeviceArray(), 'asyncos_conns', [
                    'rrd_def' => RrdDefinition::make()->addDataset('connections', 'GAUGE', 0, 50000),
                ], [
                    'connections' => $connections,
                ]);

                $this->enableGraph('asyncos_conns');
            }
        }
    }
}
