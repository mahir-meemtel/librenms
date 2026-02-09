<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS;

class Engenius extends OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        // SENAO-ENTERPRISE-INDOOR-AP-CB-MIB::entSysMode.0
        $data = \SnmpQuery::numeric()->get([
            '.1.3.6.1.4.1.14125.2.1.1.5.0',
            '.1.3.6.1.4.1.14125.3.1.1.5.0',
            '.1.3.6.1.4.1.14125.100.1.4.0',
            '.1.3.6.1.4.1.14125.100.1.6.0',
            '.1.3.6.1.4.1.14125.100.1.7.0',
            '.1.3.6.1.4.1.14125.100.1.8.0',
            '.1.3.6.1.4.1.14125.100.1.9.0',
        ])->values();

        // Sorry about the OIDs but there doesn't seem to be a matching MIB available... :-/
        if (! empty($data['.1.3.6.1.4.1.14125.100.1.8.0']) && ! empty($data['.1.3.6.1.4.1.14125.100.1.9.0'])) {
            $device->version = 'Kernel ' . $data['.1.3.6.1.4.1.14125.100.1.8.0'] . ' / Apps ' . $data['.1.3.6.1.4.1.14125.100.1.9.0'];
        } else {
            $device->version = isset($data['.1.3.6.1.4.1.14125.2.1.1.5.0']) ? 'Firmware ' . $data['.1.3.6.1.4.1.14125.2.1.1.5.0'] : null;
        }
        $device->serial = $data['.1.3.6.1.4.1.14125.100.1.7.0'] ?? null;

        // There doesn't seem to be a real hardware identification.. sysName will have to do?
        if (! empty($data['.1.3.6.1.4.1.14125.100.1.6.0'])) {
            $device->hardware = str_replace('EnGenius ', '', $device->sysName) . ' v' . $data['.1.3.6.1.4.1.14125.100.1.6.0'];
        } else {
            $device->hardware = $device->sysName . ($data['.1.3.6.1.4.1.14125.3.1.1.5.0'] ?? '');
        }

        switch ($data['.1.3.6.1.4.1.14125.100.1.4.0'] ?? null) {
            case 0:
                $device->features = 'Router mode';
                break;
            case 1:
                $device->features = 'Universal repeater mode';
                break;
            case 2:
                $device->features = 'Access Point mode';
                break;
            case 3:
                $device->features = 'Client Bridge mode';
                break;
            case 4:
                $device->features = 'Client router mode';
                break;
            case 5:
                $device->features = 'WDS Bridge mode';
                break;
        }
    }
}
