<?php
namespace ObzoraNMS\OS;

use Illuminate\Support\Str;
use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\OS;

class Symbol extends OS implements WirelessClientsDiscovery
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

        if (Str::contains(strtolower($device['hardware']), 'ap')) {
            $oid = '.1.3.6.1.4.1.388.11.2.4.2.100.10.1.18.1';

            return [
                new WirelessSensor('clients', $device['device_id'], $oid, 'symbol', 1, 'Clients'),
            ];
        }

        return [];
    }
}
