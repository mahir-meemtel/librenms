<?php
namespace ObzoraNMS\Modules;

use App\Models\Device;
use App\Observers\ModuleModelObserver;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\QosDiscovery;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\Interfaces\Polling\QosPolling;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\RRD\RrdDefinition;

class Qos implements Module
{
    use SyncsModels;

    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return [];
    }

    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice()) && $os instanceof QosDiscovery;
    }

    /**
     * Discover this module. Heavier processes can be run here
     * Run infrequently (default 4 times a day)
     *
     * @param  OS  $os
     */
    public function discover(OS $os): void
    {
        if ($os instanceof QosDiscovery) {
            $qos = $os->discoverQos();
            ModuleModelObserver::observe(\App\Models\Qos::class);
            $qos = $this->syncModels($os->getDevice(), 'qos', $qos);
            $os->setQosParents($qos);
        }
    }

    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice()) && $os instanceof QosPolling;
    }

    /**
     * Poll data for this module and update the DB / RRD.
     * Try to keep this efficient and only run if discovery has indicated there is a reason to run.
     * Run frequently (default every 5 minutes)
     *
     * @param  OS  $os
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        if ($os instanceof QosPolling) {
            // Gather our SLA's from the DB.
            $qos = $os->getDevice()->qos()
                ->where('disabled', 0)->get();

            if ($qos->isNotEmpty()) {
                // We have QoS to poll
                $os->pollQos($qos);
                $os->getDevice()->qos()->saveMany($qos);

                // Update RRD and set data rates
                foreach ($qos as $thisQos) {
                    // Generate RRD config for each graph type
                    switch ($thisQos->type) {
                        case 'routeros_simple':
                            $rrd_name = ['routeros-simplequeue', $thisQos->rrd_id];
                            $rrd_def = RrdDefinition::make()
                                ->addDataset('bytesin', 'COUNTER', 0)
                                ->addDataset('bytesout', 'COUNTER', 0)
                                ->addDataset('packetsin', 'COUNTER', 0)
                                ->addDataset('packetsout', 'COUNTER', 0)
                                ->addDataset('droppacketsin', 'COUNTER', 0)
                                ->addDataset('droppacketsout', 'COUNTER', 0);
                            $rrd_data = [
                                'bytesin' => $thisQos->last_bytes_in,
                                'bytesout' => $thisQos->last_bytes_out,
                                'packetsin' => $thisQos->last_packets_in,
                                'packetsout' => $thisQos->last_packets_out,
                                'droppacketsin' => $thisQos->last_packets_drop_in,
                                'droppacketsout' => $thisQos->last_packets_drop_out,
                            ];
                            break;
                        case 'routeros_tree':
                            $rrd_name = ['routeros-queuetree', $thisQos->rrd_id];
                            $rrd_def = RrdDefinition::make()
                                ->addDataset('bytes', 'COUNTER', 0)
                                ->addDataset('packets', 'COUNTER', 0)
                                ->addDataset('droppackets', 'COUNTER', 0);
                            $rrd_data = [
                                'bytes' => $thisQos->last_bytes_out,
                                'packets' => $thisQos->last_packets_out,
                                'droppackets' => $thisQos->last_packets_drop_out,
                            ];
                            break;
                        case 'cisco_cbqos_classmap':
                            $rrd_name = [$thisQos->rrd_id];
                            $rrd_def = RrdDefinition::make()
                                ->addDataset('postbits', 'COUNTER', 0)
                                ->addDataset('bufferdrops', 'COUNTER', 0)
                                ->addDataset('qosdrops', 'COUNTER', 0)
                                ->addDataset('prebits', 'COUNTER', 0)
                                ->addDataset('prepkts', 'COUNTER', 0)
                                ->addDataset('droppkts', 'COUNTER', 0);
                            if ($thisQos->ingress) {
                                $rrd_data = [
                                    'postbits' => $thisQos->poll_data['postbytes'],
                                    'bufferdrops' => $thisQos->poll_data['bufferdrops'],
                                    'qosdrops' => $thisQos->last_bytes_drop_in,
                                    'prebits' => $thisQos->last_bytes_in,
                                    'prepkts' => $thisQos->last_packets_in,
                                    'droppkts' => $thisQos->last_packets_drop_in,
                                ];
                            } elseif ($thisQos->egress) {
                                $rrd_data = [
                                    'postbits' => $thisQos->poll_data['postbytes'],
                                    'bufferdrops' => $thisQos->poll_data['bufferdrops'],
                                    'qosdrops' => $thisQos->last_bytes_drop_out,
                                    'prebits' => $thisQos->last_bytes_out,
                                    'prepkts' => $thisQos->last_packets_out,
                                    'droppkts' => $thisQos->last_packets_drop_out,
                                ];
                            } else {
                                // Do nothing (error was logged in poll module)
                                $rrd_name = null;
                                $rrd_data = null;
                                $rrd_def = null;
                            }
                            break;
                        case 'cisco_cbqos_policymap':
                            // No polling for the above QoS types
                            $rrd_name = null;
                            $rrd_data = null;
                            $rrd_def = null;
                            break;
                        default:
                            $rrd_name = null;
                            $rrd_data = null;
                            $rrd_def = null;
                            echo 'Queue type |' . $thisQos->type . "| has not been implemented in ObzoraNMS/Modules/Qos.php\n";
                    }
                    if (! is_null($rrd_name)) {
                        $datastore->put($os->getDeviceArray(), 'qos', ['rrd_name' => $rrd_name, 'rrd_def' => $rrd_def], $rrd_data);
                    }
                }
            }
        }
    }

    public function dataExists(Device $device): bool
    {
        return $device->qos()->exists();
    }

    /**
     * Remove all DB data for this module.
     * This will be run when the module is disabled.
     */
    public function cleanup(Device $device): int
    {
        return $device->qos()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        return [
            'qos' => $device->qos()->orderBy('title')->orderBy('snmp_idx')
                ->get()->map->makeHidden(['qos_id', 'created_at', 'updated_at', 'device_id', 'port_id', 'parent_id', 'last_polled']),
        ];
    }
}
