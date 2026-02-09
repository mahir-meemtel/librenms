<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use App\Models\EntPhysical;
use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Axos extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device);
        $cards = explode("\n", snmp_walk($this->getDeviceArray(), 'axosCardActualType', '-OQv', 'Axos-Card-MIB'));
        $card_count = [];
        foreach ($cards as $card) {
            $card_count[$card] = ($card_count[$card] ?? 0) + 1;
        }
        $device->features = implode(', ', array_map(function ($card) use ($card_count) {
            return ($card_count[$card] > 1 ? $card_count[$card] . 'x ' : '') . $card;
        }, array_keys($card_count)));
    }

    public function discoverEntityPhysical(): Collection
    {
        $inventory = new Collection;
        $physical_index = 1;

        $physical_name = \SnmpQuery::hideMib()->mibs(['CALIX-PRODUCT-MIB'])->translate($this->getDevice()->sysObjectID);
        $serial_number = \SnmpQuery::get('Axos-System-MIB::axosSystemChassisSerialNumber.0')->value();
        $inventory->push(new EntPhysical([
            'entPhysicalIndex' => $physical_index++,
            'entPhysicalDescr' => $physical_name,
            'entPhysicalContainedIn' => 0,
            'entPhysicalClass' => 'chassis',
            'entPhysicalName' => $physical_name,
            'entPhysicalSerialNum' => $serial_number,
            'entPhysicalMfgName' => 'Calix',
            'entPhysicalModelName' => $physical_name,
            'entPhysicalIsFRU' => 'false',
        ]));

        $cards = \SnmpQuery::enumStrings()->walk('Axos-Card-MIB::axosCardTable')->table(2);
        foreach ($cards as $shelf => $shelf_cards) {
            $shelf_index = $shelf * 100;
            $inventory->push(new EntPhysical([
                'entPhysicalIndex' => $shelf_index,
                'entPhysicalDescr' => "Shelf $shelf",
                'entPhysicalClass' => 'container',
                //                'entPhysicalModelName' => '100-01449', // Not known, this is E7-2 part number
                'entPhysicalContainedIn' => 1,
                'entPhysicalParentRelPos' => $shelf,
                'entPhysicalIsFRU' => 'false',
            ]));
            foreach ($shelf_cards as $card_index => $card) {
                $inventory->push(new EntPhysical([
                    'entPhysicalIndex' => $shelf_index + $card_index,
                    'entPhysicalMfgName' => 'Calix',
                    'entPhysicalDescr' => $card['Axos-Card-MIB::axosCardActualType'],
                    'entPhysicalName' => "$shelf-$card_index",
                    'entPhysicalClass' => 'module',
                    'entPhysicalModelName' => $card['Axos-Card-MIB::axosCardPartNumber'],
                    'entPhysicalSerialNum' => $card['Axos-Card-MIB::axosCardSerialNumber'],
                    'entPhysicalContainedIn' => $shelf_index,
                    'entPhysicalParentRelPos' => $card['Axos-Card-MIB::axosCardSlot'],
                    'entPhysicalSoftwareRev' => $card['Axos-Card-MIB::axosCardSoftwareVersion'],
                    'entPhysicalIsFRU' => 'true',
                ]));
            }
        }

        return $inventory;
    }
}
