<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;

class Barracudangfirewall extends OS implements OSDiscovery, OSPolling
{
    public function discoverOS(Device $device): void
    {
        if ($device->sysObjectID == '.1.3.6.1.4.1.10704.1.10') {
            $device->hardware = $device->sysName;
        }
    }

    public function pollOS(DataStorageInterface $datastore): void
    {
        // TODO move to count sensor
        $sessions = snmp_get($this->getDeviceArray(), 'firewallSessions64.8.102.119.83.116.97.116.115.0', '-OQv', 'PHION-MIB');

        if (is_numeric($sessions)) {
            $rrd_def = RrdDefinition::make()->addDataset('fw_sessions', 'GAUGE', 0);
            $fields = ['fw_sessions' => $sessions];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'barracuda_firewall_sessions', $tags, $fields);
            $this->enableGraph('barracuda_firewall_sessions');
        }
    }
}
