<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\RRD\RrdDefinition;

class Topvision extends \ObzoraNMS\OS implements OSPolling
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml
        $device->serial = snmp_getnext($this->getDeviceArray(), '.1.3.6.1.4.1.32285.11.1.1.2.1.1.1.16', '-OQv') ?: null;
        if (empty($device->hardware)) {
            $device->hardware = snmp_getnext($this->getDeviceArray(), '.1.3.6.1.4.1.32285.11.1.1.2.1.1.1.18', '-OQv') ?: null;
        }
    }

    public function pollOS(DataStorageInterface $datastore): void
    {
        $cmstats = snmp_get_multi_oid($this->getDeviceArray(), ['.1.3.6.1.4.1.32285.11.1.1.2.2.3.1.0', '.1.3.6.1.4.1.32285.11.1.1.2.2.3.6.0', '.1.3.6.1.4.1.32285.11.1.1.2.2.3.5.0']);
        if (is_numeric($cmstats['.1.3.6.1.4.1.32285.11.1.1.2.2.3.1.0'])) {
            $rrd_def = RrdDefinition::make()->addDataset('cmtotal', 'GAUGE', 0);
            $fields = [
                'cmtotal' => $cmstats['.1.3.6.1.4.1.32285.11.1.1.2.2.3.1.0'],
            ];
            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'topvision_cmtotal', $tags, $fields);
            $this->enableGraph('topvision_cmtotal');
        }

        if (is_numeric($cmstats['.1.3.6.1.4.1.32285.11.1.1.2.2.3.6.0'])) {
            $rrd_def = RrdDefinition::make()->addDataset('cmreg', 'GAUGE', 0);
            $fields = [
                'cmreg' => $cmstats['.1.3.6.1.4.1.32285.11.1.1.2.2.3.6.0'],
            ];
            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'topvision_cmreg', $tags, $fields);
            $this->enableGraph('topvision_cmreg');
        }

        if (is_numeric($cmstats['.1.3.6.1.4.1.32285.11.1.1.2.2.3.5.0'])) {
            $rrd_def = RrdDefinition::make()->addDataset('cmoffline', 'GAUGE', 0);
            $fields = [
                'cmoffline' => $cmstats['.1.3.6.1.4.1.32285.11.1.1.2.2.3.5.0'],
            ];
            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'topvision_cmoffline', $tags, $fields);
            $this->enableGraph('topvision_cmoffline');
        }
    }
}
