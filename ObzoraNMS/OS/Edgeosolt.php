<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Edgeosolt extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $hw = snmpwalk_cache_oid($this->getDeviceArray(), 'hrSWRunParameters', [], 'HOST-RESOURCES-MIB');
        foreach ($hw as $entry) {
            if (preg_match('/(?<=UBNT )(.*)(?= running on)/', $entry['hrSWRunParameters'], $matches)) {
                $this->getDevice()->hardware = $matches[0];
                break;
            }
        }
    }
}
