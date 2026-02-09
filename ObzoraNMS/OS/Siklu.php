<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessPowerDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\OS;

class Siklu extends OS implements
    WirelessFrequencyDiscovery,
    WirelessPowerDiscovery,
    WirelessRssiDiscovery,
    WirelessSnrDiscovery
{
    public function discoverOS(Device $device): void
    {
        $data = snmp_get_multi_oid($this->getDeviceArray(), [
            'rbSwBank1Running.0',
            'rbSwBank1Version.0',
            'rbSwBank2Version.0',
            'entPhysicalSerialNum.1',
        ], '-OQUs', 'RADIO-BRIDGE-MIB:ENTITY-MIB');

        $device->version = $data['rbSwBank1Running.0'] == 'running' ? $data['rbSwBank1Version.0'] : $data['rbSwBank2Version.0'];
        $device->hardware = $device->sysDescr;
        $device->serial = $data['entPhysicalSerialNum.1'];
    }

    /**
     * Discover wireless frequency.  This is in GHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        $oid = '.1.3.6.1.4.1.31926.2.1.1.4.1'; // RADIO-BRIDGE-MIB::rfOperationalFrequency.1

        return [
            new WirelessSensor('frequency', $this->getDeviceId(), $oid, 'siklu', 1, 'Frequency', null, 1, 1000),
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
        $oid = '.1.3.6.1.4.1.31926.2.1.1.42.1'; // RADIO-BRIDGE-MIB::rfTxPower.1

        return [
            new WirelessSensor('power', $this->getDeviceId(), $oid, 'siklu', 1, 'Tx Power'),
        ];
    }

    /**
     * Discover wireless RSSI (Received Signal Strength Indicator). This is in dBm. Type is rssi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        $oid = '.1.3.6.1.4.1.31926.2.1.1.19.1'; // RADIO-BRIDGE-MIB::rfAverageRssi.1

        return [
            new WirelessSensor('rssi', $this->getDeviceId(), $oid, 'siklu', 1, 'RSSI'),
        ];
    }

    /**
     * Discover wireless SNR.  This is in dB. Type is snr.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessSnr()
    {
        $oid = '.1.3.6.1.4.1.31926.2.1.1.18.1'; // RADIO-BRIDGE-MIB::rfAverageCinr.1

        return [
            new WirelessSensor('snr', $this->getDeviceId(), $oid, 'siklu', 1, 'CINR'),
        ];
    }
}
