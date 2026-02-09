<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\OS;

class Deliberant extends OS implements WirelessClientsDiscovery
{
    /**
     * Discover wireless client counts. Type is clients.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessClients()
    {
        $device = $this->getDeviceArray();
        $clients_data = snmpwalk_cache_oid($device, 'dlbDot11IfAssocNodeCount', [], 'DLB-802DOT11-EXT-MIB');

        $sensors = [];
        foreach ($clients_data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'clients',
                $device['device_id'],
                '.1.3.6.1.4.1.32761.3.5.1.2.1.1.16.' . $index,
                'deliberant',
                $index,
                'Clients'
            );
        }

        return $sensors;
    }
}
