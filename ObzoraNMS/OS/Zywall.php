<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS\Shared\Zyxel;
use ObzoraNMS\RRD\RrdDefinition;

class Zywall extends Zyxel implements OSDiscovery, OSPolling
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $device->hardware = $device->hardware ?: $device->sysDescr;
        // ZYXEL-ES-COMMON::sysSwVersionString.0
        if ($device->version && ($pos = strpos($device->version, 'ITS'))) {
            $device->version = substr($device->version, 0, $pos);
        }
    }

    public function pollOS(DataStorageInterface $datastore): void
    {
        $sessions = snmp_get($this->getDeviceArray(), '.1.3.6.1.4.1.890.1.6.22.1.6.0', '-Ovq');
        if (is_numeric($sessions)) {
            $rrd_def = RrdDefinition::make()->addDataset('sessions', 'GAUGE', 0, 3000000);
            $fields = [
                'sessions' => $sessions,
            ];
            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'zywall-sessions', $tags, $fields);
            $this->enableGraph('zywall_sessions');
        }
    }
}
