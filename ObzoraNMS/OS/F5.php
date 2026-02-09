<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;

class F5 extends OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $metadata = [
            'F5-BIGIP-APM-MIB::apmAccessStatCurrentActiveSessions.0' => [
                'dataset' => 'sessions',
                'type' => 'GAUGE',
                'name' => 'bigip_apm_sessions',
            ],
            'F5-BIGIP-SYSTEM-MIB::sysStatClientTotConns.0' => [
                'dataset' => 'ClientTotConns',
                'type' => 'COUNTER',
                'name' => 'bigip_system_client_connection_rate',
            ],
            'F5-BIGIP-SYSTEM-MIB::sysStatServerTotConns.0' => [
                'dataset' => 'ServerTotConns',
                'type' => 'COUNTER',
                'name' => 'bigip_system_server_connection_rate',
            ],
            'F5-BIGIP-SYSTEM-MIB::sysStatClientCurConns.0' => [
                'dataset' => 'ClientCurConns',
                'type' => 'GAUGE',
                'name' => 'bigip_system_client_concurrent_connections',
            ],
            'F5-BIGIP-SYSTEM-MIB::sysStatServerCurConns.0' => [
                'dataset' => 'ServerCurConns',
                'type' => 'GAUGE',
                'name' => 'bigip_system_server_concurrent_connections',
            ],
        ];

        // fetch data
        $data = \SnmpQuery::get(array_keys($metadata) + [
            'F5-BIGIP-SYSTEM-MIB::sysClientsslStatTotNativeConns.0',
            'F5-BIGIP-SYSTEM-MIB::sysClientsslStatTotCompatConns.0',
        ])->values();

        // connections
        foreach ($metadata as $key => $info) {
            if (isset($data[$key]) && is_numeric($data[$key])) {
                $rrd_def = RrdDefinition::make()->addDataset($info['dataset'], $info['type'], 0);
                $fields = [
                    $info['dataset'] => $data[$key],
                ];
                $tags = ['rrd_def' => $rrd_def];
                $datastore->put($this->getDeviceArray(), $info['name'], $tags, $fields);
                $this->enableGraph($info['name']);
            }
        }

        // SSL TPS
        if (isset($data['F5-BIGIP-SYSTEM-MIB::sysClientsslStatTotNativeConns.0'], $data['F5-BIGIP-SYSTEM-MIB::sysClientsslStatTotCompatConns.0'])) {
            $rrd_def = RrdDefinition::make()
                ->addDataset('TotNativeConns', 'COUNTER', 0)
                ->addDataset('TotCompatConns', 'COUNTER', 0);
            $fields = [
                'TotNativeConns' => $data['F5-BIGIP-SYSTEM-MIB::sysClientsslStatTotNativeConns.0'],
                'TotCompatConns' => $data['F5-BIGIP-SYSTEM-MIB::sysClientsslStatTotCompatConns.0'],
            ];
            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'bigip_system_tps', $tags, $fields);
            $this->enableGraph('bigip_system_tps');
        }
    }
}
