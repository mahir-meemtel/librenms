<?php
namespace ObzoraNMS\OS;

use App\Models\Location;
use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Bats extends OS implements
    OSDiscovery,
    WirelessSnrDiscovery,
    WirelessRssiDiscovery
{
    public function fetchLocation(): Location
    {
        $response = \SnmpQuery::get([
            'AATS-MIB::networkGPSLatitudeFloat.0',
            'AATS-MIB::networkGPSLongitudeFloat.0',
            'AATS-MIB::status.0',
        ]);

        $lat = $response->value('AATS-MIB::networkGPSLatitudeFloat.0');
        $lng = $response->value('AATS-MIB::networkGPSLongitudeFloat.0');
        $pointing = $response->value('AATS-MIB::status.0');

        return new Location([
            'location' => 'At ' . (string) $lat . ', ' . (string) $lng . '. ' . $pointing,
            'lat' => $lat,
            'lng' => $lng,
        ]);
    }

    public function discoverWirelessSnr()
    {
        $oid = '.1.3.6.1.4.1.37069.1.2.5.3.0';

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'bats', 0, 'SNR'),
        ];
    }

    public function discoverWirelessRssi()
    {
        $oid = '.1.3.6.1.4.1.37069.1.2.4.3.0';

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'bats', 0, 'RSSI'),
        ];
    }
}
