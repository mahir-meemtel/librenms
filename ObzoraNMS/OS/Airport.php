<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\OS;

class Airport extends OS implements WirelessClientsDiscovery
{
    /**
     * Discover wireless client counts. Type is clients.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessClients()
    {
        $oid = '.1.3.6.1.4.1.63.501.3.2.1.0'; //AIRPORT-BASESTATION-3-MIB::wirelessNumber.0

        return [
            new WirelessSensor('clients', $this->getDeviceId(), $oid, 'airport', 0, 'Clients'),
        ];
    }
}
