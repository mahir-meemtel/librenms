<?php
namespace ObzoraNMS\OS;

use App\Facades\PortCache;
use App\Models\PortVlan;
use App\Models\Vlan;
use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Discovery\VlanDiscovery;
use ObzoraNMS\Interfaces\Discovery\VlanPortDiscovery;
use ObzoraNMS\OS;
use SnmpQuery;

class Aos7 extends OS implements VlanDiscovery, VlanPortDiscovery
{
    public function discoverVlans(): Collection
    {
        if (($QBridgeMibVlans = parent::discoverVlans())->isNotEmpty()) {
            return $QBridgeMibVlans;
        }

        return SnmpQuery::mibDir('nokia/aos7')->walk('ALCATEL-IND1-VLAN-MGR-MIB::vlanDescription')
            ->mapTable(function ($vlans, $vlan_id) {
                return new Vlan([
                    'vlan_vlan' => $vlan_id,
                    'vlan_name' => $vlans['ALCATEL-IND1-VLAN-MGR-MIB::vlanDescription'] ?? null,
                    'vlan_domain' => 1,
                    'vlan_type' => null,
                ]);
            });
    }

    public function discoverVlanPorts(Collection $vlans): Collection
    {
        if (($QBridgeMibPorts = parent::discoverVlanPorts($vlans))->isNotEmpty()) {
            return $QBridgeMibPorts;
        }

        return SnmpQuery::mibDir('nokia/aos7')->walk('ALCATEL-IND1-VLAN-MGR-MIB::vpaType')
            ->mapTable(function ($data, $vpaVlanNumber, $vpaIfIndex) {
                $baseport = $this->bridgePortFromIfIndex($vpaIfIndex);
                if (! $baseport) {
                    return null;
                }

                return new Portvlan([
                    'vlan' => $vpaVlanNumber,
                    'baseport' => $baseport,
                    'untagged' => $data['ALCATEL-IND1-VLAN-MGR-MIB::vpaType'] === '1' ? 1 : 0,
                    'port_id' => PortCache::getIdFromIfIndex($vpaIfIndex, $this->getDeviceId()) ?? 0, // ifIndex from device
                ]);
            })->filter();
    }
}
