<?php
namespace ObzoraNMS\OS;

use App\Facades\PortCache;
use App\Models\Device;
use App\Models\Transceiver;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\Interfaces\Discovery\TransceiverDiscovery;
use ObzoraNMS\OS;
use SnmpQuery;

class Awplus extends OS implements OSDiscovery, TransceiverDiscovery
{
    public function discoverOS(Device $device): void
    {
        //$hardware and $serial use first return as the OID for these is not always fixed.
        //However, the first OID is the device baseboard.

        $response = SnmpQuery::walk(['AT-RESOURCE-MIB::rscBoardName', 'AT-RESOURCE-MIB::rscBoardSerialNumber']);
        $hardware = $response->value('AT-RESOURCE-MIB::rscBoardName');
        $serial = $response->value('AT-RESOURCE-MIB::rscBoardSerialNumber');

        // SBx8100 platform has line cards show up first in "rscBoardName" above.
        //Instead use sysObjectID.0

        if (Str::contains($hardware, 'SBx81')) {
            $hardware = SnmpQuery::hideMib()->mibs(['AT-PRODUCT-MIB'])->translate($device->sysObjectID);
            $hardware = str_replace('at', 'AT-', $hardware);

            // Features and Serial is set to Controller card 1.5 or 1.6
            $features = $response->value([
                'AT-RESOURCE-MIB::rscBoardName.5.6',
                'AT-RESOURCE-MIB::rscBoardName.6.6',
            ]);
            $serial = $response->value([
                'AT-RESOURCE-MIB::rscBoardSerialNumber.5.6',
                'AT-RESOURCE-MIB::rscBoardSerialNumber.6.6',
            ]);
        }

        $device->version = SnmpQuery::get('AT-SETUP-MIB::currSoftVersion.0')->value();
        $device->serial = $serial;
        $device->hardware = $hardware;
        $device->features = $features ?? null;
    }

    public function discoverTransceivers(): Collection
    {
        return \SnmpQuery::enumStrings()->walk('AT-SYSINFO-MIB::atPortInfoTransceiverTable')
            ->mapTable(function ($data, $ifIndex) {
                return new Transceiver([
                    'port_id' => (int) PortCache::getIdFromIfIndex($ifIndex, $this->getDevice()),
                    'index' => $ifIndex,
                    'type' => $data['AT-SYSINFO-MIB::atPortInfoTransceiverType'] ?? null,
                    'entity_physical_index' => $ifIndex,
                ]);
            });
    }
}
