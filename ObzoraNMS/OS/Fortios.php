<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\OS\Shared\Fortinet;
use ObzoraNMS\RRD\RrdDefinition;

class Fortios extends Fortinet implements OSPolling
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $device->hardware = $device->hardware ?: $this->getHardwareName();
        $device->features = snmp_get($this->getDeviceArray(), 'fmDeviceEntMode.1', '-OQv', 'FORTINET-FORTIMANAGER-FORTIANALYZER-MIB') == 'fmg-faz' ? 'with Analyzer features' : null;
    }

    public function pollOS(DataStorageInterface $datastore): void
    {
        // Log rate only for FortiAnalyzer features enabled FortiManagers
        if ($this->getDevice()->features == 'with Analyzer features') {
            $log_rate = snmp_get($this->getDeviceArray(), '.1.3.6.1.4.1.12356.103.2.1.9.0', '-Ovq');
            $log_rate = str_replace(' logs per second', '', $log_rate);
            $rrd_def = RrdDefinition::make()->addDataset('lograte', 'GAUGE', 0, 100000000);
            $fields = ['lograte' => $log_rate];
            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'fortios_lograte', $tags, $fields);
            $this->enableGraph('fortios_lograte');
        }
    }
}
