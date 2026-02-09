<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Device\WirelessSensor;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessNoiseFloorDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRateDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessSnrDiscovery;
use ObzoraNMS\Interfaces\Discovery\Sensors\WirelessUtilizationDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\Interfaces\Polling\Sensors\WirelessFrequencyPolling;
use ObzoraNMS\OS;
use ObzoraNMS\RRD\RrdDefinition;

class XirrusAos extends OS implements
    OSPolling,
    WirelessClientsDiscovery,
    WirelessFrequencyDiscovery,
    WirelessFrequencyPolling,
    WirelessNoiseFloorDiscovery,
    WirelessUtilizationDiscovery,
    WirelessRateDiscovery,
    WirelessRssiDiscovery,
    WirelessSnrDiscovery
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $associations = [];

        // if this config flag is true, don't poll for stations
        // this in case of large APs which may have many stations
        // to prevent causing long polling times
        if (\App\Facades\ObzoraConfig::get('xirrus_disable_stations') != true) {
            // station associations
            // custom RRDs and graph as each AP may have 16 radios
            $assoc = snmpwalk_cache_oid($this->getDeviceArray(), 'XIRRUS-MIB::stationAssociationIAP', [], 'XIRRUS-MIB');
            foreach ($assoc as $s) {
                $radio = array_pop($s);
                $associations[$radio] = (int) $associations[$radio] + 1;
            }
            unset($radio);
            unset($assoc);
            // write to rrds
            foreach ($associations as $radio => $count) {
                $measurement = 'xirrus_users';
                $rrd_name = [$measurement, $radio];
                $rrd_def = RrdDefinition::make()->addDataset('stations', 'GAUGE', 0, 3200);
                $fields = [
                    'stations' => $count,
                ];
                $tags = ['radio' => $radio, 'rrd_name' => $rrd_name, 'rrd_def' => $rrd_def];
                $datastore->put($this->getDeviceArray(), $measurement, $tags, $fields);
            }
            $this->enableGraph('xirrus_stations');
        }
    }

    /**
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessClients()
    {
        $oid = '.1.3.6.1.4.1.21013.1.2.12.1.2.22.0'; // XIRRUS-MIB::globalNumStations.0

        return [
            new WirelessSensor('clients', $this->getDeviceId(), $oid, 'xirrus', 0, 'Clients'),
        ];
    }

    /**
     * Discover wireless frequency.  This is in MHz. Type is frequency.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessFrequency()
    {
        return $this->discoverSensor('frequency', 'realtimeMonitorChannel', '.1.3.6.1.4.1.21013.1.2.24.7.1.3.');
    }

    /**
     * Poll wireless frequency as MHz
     * The returned array should be sensor_id => value pairs
     *
     * @param  array  $sensors  Array of sensors needed to be polled
     * @return array of polled data
     */
    public function pollWirelessFrequency(array $sensors)
    {
        return $this->pollWirelessChannelAsFrequency($sensors);
    }

    /**
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessNoiseFloor()
    {
        return $this->discoverSensor('noise-floor', 'realtimeMonitorNoiseFloor', '.1.3.6.1.4.1.21013.1.2.24.7.1.10.');
    }

    /**
     * Discover wireless rate. This is in bps. Type is rate.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRate()
    {
        return $this->discoverSensor('rate', 'realtimeMonitorAverageDataRate', '.1.3.6.1.4.1.21013.1.2.24.7.1.7.');
    }

    /**
     * Discover wireless RSSI (Received Signal Strength Indicator). This is in dBm. Type is rssi.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessRssi()
    {
        return $this->discoverSensor('rssi', 'realtimeMonitorAverageRSSI', '.1.3.6.1.4.1.21013.1.2.24.7.1.8.');
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
        return $this->discoverSensor('snr', 'realtimeMonitorSignalToNoiseRatio', '.1.3.6.1.4.1.21013.1.2.24.7.1.9.');
    }

    /**
     * Discover wireless utilization.  This is in %. Type is utilization.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array Sensors
     */
    public function discoverWirelessUtilization()
    {
        return $this->discoverSensor('utilization', 'realtimeMonitorDot11Busy', '.1.3.6.1.4.1.21013.1.2.24.7.1.11.');
    }

    private function discoverSensor($type, $oid, $oid_num_prefix)
    {
        $names = $this->getCacheByIndex('realtimeMonitorIfaceName', 'XIRRUS-MIB');
        $nf = snmpwalk_cache_oid($this->getDeviceArray(), $oid, [], 'XIRRUS-MIB');

        $sensors = [];
        foreach ($nf as $index => $entry) {
            $sensors[] = new WirelessSensor(
                $type,
                $this->getDeviceId(),
                $oid_num_prefix . $index,
                'xirrus',
                $index,
                $names[$index],
                $type == 'frequency' ? WirelessSensor::channelToFrequency($entry[$oid]) : $entry[$oid]
            );
        }

        return $sensors;
    }
}
