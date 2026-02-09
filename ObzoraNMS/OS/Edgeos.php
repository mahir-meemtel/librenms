<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use App\Models\EntPhysical;
use ObzoraNMS\OS\Traits\EntityMib;
use ObzoraNMS\Util\StringHelpers;

class Edgeos extends \ObzoraNMS\OS
{
    use EntityMib {
        EntityMib::discoverEntityPhysical as discoverBaseEntityPhysical;
    }

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

    public function discoverEntityPhysical(): \Illuminate\Support\Collection
    {
        return $this->discoverBaseEntityPhysical()->each(function (EntPhysical $entity) {
            // clean garbage in fields "...............\n00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00"
            $entity->entPhysicalDescr = StringHelpers::trimHexGarbage($entity->entPhysicalDescr);
            $entity->entPhysicalName = StringHelpers::trimHexGarbage($entity->entPhysicalName);
            $entity->entPhysicalVendorType = StringHelpers::trimHexGarbage($entity->entPhysicalVendorType);
        });
    }
}
