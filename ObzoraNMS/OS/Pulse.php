<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\RRD\RrdDefinition;
use SnmpQuery;

class Pulse extends \ObzoraNMS\OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $users = SnmpQuery::get('PULSESECURE-PSG-MIB::iveConcurrentUsers.0')->value();

        if (is_numeric($users)) {
            $rrd_def = RrdDefinition::make()->addDataset('users', 'GAUGE', 0);

            $fields = [
                'users' => $users,
            ];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'pulse_users', $tags, $fields);
            $this->enableGraph('pulse_users');
        }
    }
}
