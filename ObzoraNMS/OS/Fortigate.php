<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessApCountDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS\Shared\Fortinet;
use ObzoraNMS\RRD\RrdDefinition;

class Fortigate extends Fortinet implements
    OSPolling,
    WirelessClientsDiscovery,
    WirelessApCountDiscovery
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $device->hardware = $device->hardware ?: $this->getHardwareName();
    }

    public function pollOS(DataStorageInterface $datastore): void
    {
        $sessions = snmp_get($this->getDeviceArray(), 'FORTINET-FORTIGATE-MIB::fgSysSesCount.0', '-Ovq');
        if (is_numeric($sessions)) {
            $rrd_def = RrdDefinition::make()->addDataset('sessions', 'GAUGE', 0, 3000000);

            Log::info("Sessions: $sessions");
            $fields = [
                'sessions' => $sessions,
            ];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'fortigate_sessions', $tags, $fields);
            $this->enableGraph('fortigate_sessions');
        }

        $cpu_usage = snmp_get($this->getDeviceArray(), 'FORTINET-FORTIGATE-MIB::fgSysCpuUsage.0', '-Ovq');
        if (is_numeric($cpu_usage)) {
            $rrd_def = RrdDefinition::make()->addDataset('LOAD', 'GAUGE', -1, 100);

            Log::info("CPU: $cpu_usage%");
            $fields = [
                'LOAD' => $cpu_usage,
            ];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'fortigate_cpu', $tags, $fields);
            $this->enableGraph('fortigate_cpu');
        }
    }

    public function discoverWirelessClients()
    {
        $oid = '.1.3.6.1.4.1.12356.101.14.2.7.0';

        return [
            new WirelessSensor('clients', $this->getDeviceId(), $oid, 'fortigate', 1, 'Clients: Total'),
        ];
    }

    public function discoverWirelessApCount()
    {
        $oid = '.1.3.6.1.4.1.12356.101.14.2.5.0';

        return [
            new WirelessSensor('ap-count', $this->getDeviceId(), $oid, 'fortigate', 1, 'Connected APs'),
        ];
    }
}
