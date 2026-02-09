<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessCellDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessChannelDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrpDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRsrqDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS\Shared\Cisco;
use ObzoraNMS\OS\Traits\CiscoCellular;

class Ios extends Cisco implements
    WirelessCellDiscovery,
    WirelessChannelDiscovery,
    WirelessClientsDiscovery,
    WirelessRssiDiscovery,
    WirelessRsrqDiscovery,
    WirelessRsrpDiscovery,
    WirelessSnrDiscovery
{
    use CiscoCellular;

    /**
     * @return WirelessSensor[] Sensors
     */
    public function discoverWirelessClients(): array
    {
        $device = $this->getDevice();

        if (empty($device->hardware) || (! str_starts_with($device->hardware, 'AIR-') && ! str_contains($device->hardware, 'ciscoAIR'))) {
            // unsupported IOS hardware
            return [];
        }

        $data = \SnmpQuery::walk('CISCO-DOT11-ASSOCIATION-MIB::cDot11ActiveWirelessClients')->table(1);

        if (empty($data)) {
            return [];
        }

        $this->mapToEntPhysical($data);

        $sensors = [];
        foreach ($data as $ifIndex => $entry) {
            $sensors[] = new WirelessSensor(
                'clients',
                $device['device_id'],
                ".1.3.6.1.4.1.9.9.273.1.1.2.1.1.$ifIndex",
                'ios',
                $ifIndex,
                $entry['entPhysicalDescr'],
                $entry['cDot11ActiveWirelessClients'],
                1,
                1,
                'sum',
                null,
                40,
                null,
                30,
                $entry['entPhysicalIndex'],
                'ports'
            );
        }

        return $sensors;
    }

    private function mapToEntPhysical(array &$data): array
    {
        // try DB first
        $dbMap = $this->getDevice()->entityPhysical;
        if ($dbMap->isNotEmpty()) {
            foreach ($data as $ifIndex => $_unused) {
                foreach ($dbMap as $entPhys) {
                    if ($entPhys->ifIndex === $ifIndex) {
                        $data[$ifIndex]['entPhysicalIndex'] = $entPhys->entPhysicalIndex;
                        $data[$ifIndex]['entPhysicalDescr'] = $entPhys->entPhysicalDescr;
                        break;
                    }
                }
            }

            return $data;
        }

        $entPhys = \SnmpQuery::walk('ENTITY-MIB::entPhysicalDescr')->table(1);

        // fixup incorrect/missing entPhysicalIndex mapping (doesn't use entAliasMappingIdentifier for some reason)
        foreach ($data as $ifIndex => $_unused) {
            foreach ($entPhys as $entIndex => $ent) {
                $descr = $ent['ENTITY-MIB::entPhysicalDescr'];
                unset($entPhys[$entIndex]); // only use each one once

                if (str_ends_with($descr, 'Radio')) {
                    d_echo("Mapping entPhysicalIndex $entIndex to ifIndex $ifIndex\n");
                    $data[$ifIndex]['entPhysicalIndex'] = $entIndex;
                    $data[$ifIndex]['entPhysicalDescr'] = $descr;
                    break;
                }
            }
        }

        return $data;
    }
}
