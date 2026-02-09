<?php
namespace ObzoraNMS\OS\Traits;

use App\Models\EntPhysical;
use Illuminate\Support\Collection;

trait EntityMib
{
    public function discoverEntityPhysical(): Collection
    {
        $snmpQuery = \SnmpQuery::hideMib()->enumStrings();
        if (isset($this->entityVendorTypeMib)) {
            $snmpQuery = $snmpQuery->mibs([$this->entityVendorTypeMib]);
        }
        $data = $snmpQuery->walk('ENTITY-MIB::entPhysicalTable');

        if (! $data->isValid()) {
            return new Collection;
        }

        $entPhysicalToIfIndexMap = $this->getIfIndexEntPhysicalMap();

        return $data->mapTable(function ($data, $entityPhysicalIndex) use ($entPhysicalToIfIndexMap) {
            $entityPhysical = new EntPhysical($data);
            $entityPhysical->entPhysicalIndex = $entityPhysicalIndex;
            // get ifIndex. also if parent has an ifIndex, set it too
            $entityPhysical->ifIndex = $entPhysicalToIfIndexMap[$entityPhysicalIndex] ?? $entPhysicalToIfIndexMap[$entityPhysical->entPhysicalContainedIn] ?? null;

            return $entityPhysical;
        });
    }

    /**
     * @return array<int, int>
     */
    protected function getIfIndexEntPhysicalMap(): array
    {
        $mapping = \SnmpQuery::cache()->walk('ENTITY-MIB::entAliasMappingIdentifier')->table(2);
        $map = [];

        foreach ($mapping as $entityPhysicalIndex => $data) {
            $id = $data[0]['ENTITY-MIB::entAliasMappingIdentifier'] ?? $data[1]['ENTITY-MIB::entAliasMappingIdentifier'] ?? null;
            if ($id && preg_match('/ifIndex[\[.](\d+)/', $id, $matches)) {
                $map[(int) $entityPhysicalIndex] = (int) $matches[1];
            }
        }

        return $map;
    }
}
