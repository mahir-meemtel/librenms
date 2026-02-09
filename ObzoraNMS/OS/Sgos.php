<?php
namespace ObzoraNMS\OS;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ObzoraNMS\Device\Processor;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;

class Sgos extends OS implements ProcessorDiscovery, OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $oid_list = [
            'sgProxyHttpClientRequestRate.0',
            'sgProxyHttpClientConnections.0',
            'sgProxyHttpClientConnectionsActive.0',
            'sgProxyHttpClientConnectionsIdle.0',
            'sgProxyHttpServerConnections.0',
            'sgProxyHttpServerConnectionsActive.0',
            'sgProxyHttpServerConnectionsIdle.0',
        ];

        $sgos = snmp_get_multi($this->getDeviceArray(), $oid_list, '-OUQs', 'BLUECOAT-SG-PROXY-MIB');

        if (is_numeric($sgos[0]['sgProxyHttpClientRequestRate'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('requests', 'GAUGE', 0),
            ];
            $fields = [
                'requests' => $sgos[0]['sgProxyHttpClientRequestRate'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_average_requests', $tags, $fields);

            $this->enableGraph('sgos_average_requests');
            Log::info(' HTTP Req Rate');
        }

        if (is_numeric($sgos[0]['sgProxyHttpClientConnections'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('client_conn', 'GAUGE', 0),
            ];
            $fields = [
                'client_conn' => $sgos[0]['sgProxyHttpClientConnections'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_client_connections', $tags, $fields);

            $this->enableGraph('sgos_client_connections');
            Log::info(' Client Conn');
        }

        if (is_numeric($sgos[0]['sgProxyHttpServerConnections'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('server_conn', 'GAUGE', 0),
            ];
            $fields = [
                'server_conn' => $sgos[0]['sgProxyHttpServerConnections'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_server_connections', $tags, $fields);

            $this->enableGraph('sgos_server_connections');
            Log::info(' Server Conn');
        }

        if (is_numeric($sgos[0]['sgProxyHttpClientConnectionsActive'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('client_conn_active', 'GAUGE', 0),
            ];
            $fields = [
                'client_conn_active' => $sgos[0]['sgProxyHttpClientConnectionsActive'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_client_connections_active', $tags, $fields);

            $this->enableGraph('sgos_client_connections_active');
            Log::info(' Client Conn Active');
        }

        if (is_numeric($sgos[0]['sgProxyHttpServerConnectionsActive'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('server_conn_active', 'GAUGE', 0),
            ];
            $fields = [
                'server_conn_active' => $sgos[0]['sgProxyHttpServerConnectionsActive'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_server_connections_active', $tags, $fields);

            $this->enableGraph('sgos_server_connections_active');
            Log::info(' Server Conn Active');
        }

        if (is_numeric($sgos[0]['sgProxyHttpClientConnectionsIdle'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('client_idle', 'GAUGE', 0),
            ];
            $fields = [
                'client_idle' => $sgos[0]['sgProxyHttpClientConnectionsIdle'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_client_connections_idle', $tags, $fields);

            $this->enableGraph('sgos_client_connections_idle');
            Log::info(' Client Conne Idle');
        }

        if (is_numeric($sgos[0]['sgProxyHttpServerConnectionsIdle'] ?? null)) {
            $tags = [
                'rrd_def' => RrdDefinition::make()->addDataset('server_idle', 'GAUGE', 0),
            ];
            $fields = [
                'server_idle' => $sgos[0]['sgProxyHttpServerConnectionsIdle'],
            ];

            $datastore->put($this->getDeviceArray(), 'sgos_server_connections_idle', $tags, $fields);

            $this->enableGraph('sgos_server_connections_idle');
            Log::info(' Server Conn Idle');
        }
    }

    /**
     * Discover processors.
     * Returns an array of ObzoraNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        $data = snmpwalk_group($this->getDeviceArray(), 'sgProxyCpuCoreBusyPerCent', 'BLUECOAT-SG-PROXY-MIB');

        $processors = [];
        $count = 1;
        foreach ($data as $index => $entry) {
            $processors[] = Processor::discover(
                $this->getName(),
                $this->getDeviceId(),
                ".1.3.6.1.4.1.3417.2.11.2.4.1.8.$index",
                Str::padLeft($index, 2, '0'),
                "Processor $count",
                1,
                $entry['sgProxyCpuCoreBusyPerCent']
            );

            $count++;
        }

        return $processors;
    }
}
