<?php
namespace ObzoraNMS\OS;

use App\Facades\PortCache;
use App\Models\Ipv4Mac;
use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\ArpTableDiscovery;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\RRD\RrdDefinition;
use ObzoraNMS\Util\Mac;
use SnmpQuery;

class Screenos extends \ObzoraNMS\OS implements OSPolling, ArpTableDiscovery
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        $sess_data = snmp_get_multi_oid($this->getDeviceArray(), [
            '.1.3.6.1.4.1.3224.16.3.2.0',
            '.1.3.6.1.4.1.3224.16.3.3.0',
            '.1.3.6.1.4.1.3224.16.3.4.0',
        ]);

        if (! empty($sess_data)) {
            [$sessalloc, $sessmax, $sessfailed] = array_values($sess_data);

            $rrd_def = RrdDefinition::make()
                ->addDataset('allocate', 'GAUGE', 0, 3000000)
                ->addDataset('max', 'GAUGE', 0, 3000000)
                ->addDataset('failed', 'GAUGE', 0, 1000);

            $fields = [
                'allocate' => $sessalloc,
                'max' => $sessmax,
                'failed' => $sessfailed,
            ];

            $tags = ['rrd_def' => $rrd_def];
            $datastore->put($this->getDeviceArray(), 'screenos_sessions', $tags, $fields);

            $this->enableGraph('screenos_sessions');
        }
    }

    public function discoverArpTable(): Collection
    {
        $nsIpArpTable = SnmpQuery::walk('NETSCREEN-IP-ARP-MIB::nsIpArpTable')->table(1);

        if (! empty($nsIpArpTable)) {
            $nsIfInfo = array_flip(SnmpQuery::walk('NETSCREEN-INTERFACE-MIB::nsIfInfo')->pluck());
        }

        $arp = new Collection;

        foreach ($nsIpArpTable as $data) {
            $ifIndex = $nsIfInfo[$data['NETSCREEN-IP-ARP-MIB::nsIpArpIfIdx']];

            $arp->push(new Ipv4Mac([
                'port_id' => (int) PortCache::getIdFromIfIndex($ifIndex, $this->getDevice()),
                'mac_address' => Mac::parse($data['NETSCREEN-IP-ARP-MIB::nsIpArpMac'])->hex(),
                'ipv4_address' => $data['NETSCREEN-IP-ARP-MIB::nsIpArpIp'],
            ]));
        }

        return $arp;
    }
}
