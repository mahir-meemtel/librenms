<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessNoiseFloorDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Cnpilote extends OS implements
    WirelessClientsDiscovery,
    WirelessSnrDiscovery,
    WirelessPowerDiscovery,
    WirelessNoiseFloorDiscovery
{
    /**
     * Discover wireless client counts. Type is clients.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessClients()
    {
        $oid = '.1.3.6.1.4.1.17713.22.1.1.1.14.0'; //CAMBIUM-MIB::cambiumAPTotalClients.0

        return [
            new WirelessSensor('clients', $this->getDeviceId(), $oid, 'cnpilot', 1, 'Clients'),
        ];
    }

    /**
     * Discover wireless SNR.  This is in dB. Type is snr.
     * Formula: SNR = Signal or Rx Power - Noise Floor
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSnr()
    {
        $oid = '.1.3.6.1.4.1.17713.22.1.3.1.11.0'; //CAMBIUM-MIB::cambiumClientSNR.0

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'cnpilot', 1, 'SNR'),
        ];
    }

    /**
     * Discover wireless tx or rx power. This is in dBm. Type is power.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessPower()
    {
        $oid = '.1.3.6.1.4.1.17713.22.1.2.1.8.0'; //CAMBIUM-MIB::cambiumRadioTransmitPower.0

        return [
            new WirelessSensor('power', $this->getDeviceId(), $oid, 'cnpilot', 1, 'Transmit Power'),
        ];
    }

    /**
     * Discover wireless noise floor. This is in dBm/Hz. Type is noise-floor.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessNoiseFloor()
    {
        $oid = '.1.3.6.1.4.1.17713.22.1.2.1.16.0'; //CAMBIUM-MIB::cambiumRadioNoiseFloor.0

        return [
            new WirelessSensor('noise-floor', $this->getDeviceId(), $oid, 'cnpilot', 1, 'Radio Noise Floor'),
        ];
    }
}
