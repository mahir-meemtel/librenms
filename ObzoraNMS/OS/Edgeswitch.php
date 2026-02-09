<?php
namespace ObzoraNMS\OS;

use App\Facades\PortCache;
use App\Models\Ipv4Mac;
use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Discovery\ArpTableDiscovery;
use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\Interfaces\Polling\ProcessorPolling;
use ObzoraNMS\OS;
use ObzoraNMS\Util\Mac;

class Edgeswitch extends OS implements ProcessorDiscovery, ProcessorPolling, ArpTableDiscovery
{
    use Traits\VxworksProcessorUsage;

    public function discoverArpTable(): Collection
    {
        return \SnmpQuery::walk('EdgeSwitch-SWITCHING-MIB::agentDynamicDsBindingTable')
            ->mapTable(function ($data) {
                return new Ipv4Mac([
                    'port_id' => (int) PortCache::getIdFromIfIndex($data['EdgeSwitch-SWITCHING-MIB::agentDynamicDsBindingIfIndex'], $this->getDevice()),
                    'mac_address' => Mac::parse($data['EdgeSwitch-SWITCHING-MIB::agentDynamicDsBindingMacAddr'])->hex(),
                    'ipv4_address' => $data['EdgeSwitch-SWITCHING-MIB::agentDynamicDsBindingIpAddr'],
                ]);
            });
    }
}
