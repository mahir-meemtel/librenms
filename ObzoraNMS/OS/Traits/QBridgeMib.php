<?php
namespace ObzoraNMS\OS\Traits;

use App\Facades\PortCache;
use App\Models\PortVlan;
use App\Models\Vlan;
use Illuminate\Support\Collection;
use ObzoraNMS\Util\StringHelpers;
use SnmpQuery;

trait QBridgeMib
{
    private function discoverIetfQBridgeMibVlans(): Collection
    {
        return SnmpQuery::walk('Q-BRIDGE-MIB::dot1qVlanStaticName')
            ->mapTable(function ($data, $vlan_id) {
                return new Vlan([
                    'vlan_vlan' => $vlan_id,
                    'vlan_domain' => 1,
                    'vlan_name' => $data['Q-BRIDGE-MIB::dot1qVlanStaticName'] ?? '',
                ]);
            });
    }

    private function discoverIeeeQBridgeMibVlans(): Collection
    {
        return SnmpQuery::walk('IEEE8021-Q-BRIDGE-MIB::ieee8021QBridgeVlanStaticName')
            ->mapTable(function ($data, $vlan_domain_id, $vlan_id) {
                return new Vlan([
                    'vlan_vlan' => $vlan_id,
                    'vlan_domain' => $vlan_domain_id,
                    'vlan_name' => $data['IEEE8021-Q-BRIDGE-MIB::ieee8021QBridgeVlanStaticName'] ?? '',
                ]);
            });
    }

    private function discoverIetfQBridgeMibPorts(): Collection
    {
        $ports = new Collection;

        $vlanVersion = SnmpQuery::get('Q-BRIDGE-MIB::dot1qVlanVersionNumber.0')->value();

        if ($vlanVersion < 1 || $vlanVersion > 2) {
            return $ports;
        }

        // fetch vlan data
        $port_data = SnmpQuery::walk([
            'Q-BRIDGE-MIB::dot1qVlanCurrentUntaggedPorts',
            'Q-BRIDGE-MIB::dot1qVlanCurrentEgressPorts',
        ])->table(2);

        if (empty($port_data)) {
            // fall back to static
            $port_data = SnmpQuery::walk([
                'Q-BRIDGE-MIB::dot1qVlanStaticUntaggedPorts',
                'Q-BRIDGE-MIB::dot1qVlanStaticEgressPorts',
            ])->table(1);
        } else {
            // collapse timefilter from dot1qVlanCurrentTable results to only the newest
            $port_data = array_reduce($port_data, function ($result, $time_data) {
                foreach ($time_data as $vlan_id => $vlan_data) {
                    $result[$vlan_id] = isset($result[$vlan_id]) ? array_merge($result[$vlan_id], $vlan_data) : $vlan_data;
                }

                return $result;
            }, []);
        }

        foreach ($port_data as $vlan_id => $vlan) {
            //portmap for untagged ports
            $untagged = $vlan['Q-BRIDGE-MIB::dot1qVlanCurrentUntaggedPorts'] ?? $vlan['Q-BRIDGE-MIB::dot1qVlanStaticUntaggedPorts'] ?? '';
            $untagged_ids = StringHelpers::bitsToIndices($untagged);
            //portmap for members ports (might be tagged)
            $all = $vlan['Q-BRIDGE-MIB::dot1qVlanCurrentEgressPorts'] ?? $vlan['Q-BRIDGE-MIB::dot1qVlanStaticEgressPorts'] ?? '';
            $egress_ids = StringHelpers::bitsToIndices($all);

            foreach ($egress_ids as $baseport) {
                $ifIndex = $this->ifIndexFromBridgePort($baseport);
                if ($ifIndex === 0) {
                    // debug statements intentionally omitted due to possible high vlan/port counts
                    continue;
                }

                $port_id = PortCache::getIdFromIfIndex($ifIndex, $this->getDeviceId());
                if ($port_id === null) {
                    continue;
                }

                $ports->push(new PortVlan([
                    'vlan' => $vlan_id,
                    'baseport' => $baseport,
                    'untagged' => in_array($baseport, $untagged_ids) ? 1 : 0,
                    'port_id' => $port_id,
                ]));
            }
        }

        return $ports;
    }

    private function discoverIeeeQBridgeMibPorts(): Collection
    {
        $ports = new Collection;

        $port_data = SnmpQuery::walk([
            'IEEE8021-Q-BRIDGE-MIB::ieee8021QBridgeVlanStaticUntaggedPorts',
            'IEEE8021-Q-BRIDGE-MIB::ieee8021QBridgeVlanStaticEgressPorts',
        ])->table(2);

        if (empty($port_data)) {
            return $ports;
        }

        foreach ($port_data as $vlan_domain_id => $vlan_domains) {
            foreach ($vlan_domains as $vlan_id => $data) {
                //portmap for untagged ports
                $untagged_ids = StringHelpers::bitsToIndices($data['IEEE8021-Q-BRIDGE-MIB::ieee8021QBridgeVlanStaticUntaggedPorts'] ?? '');

                //portmap for members ports (might be tagged)
                $egress_ids = StringHelpers::bitsToIndices($data['IEEE8021-Q-BRIDGE-MIB::ieee8021QBridgeVlanStaticEgressPorts'] ?? '');

                foreach ($egress_ids as $baseport) {
                    $ports->push(new PortVlan([
                        'vlan' => $vlan_id,
                        'baseport' => $baseport,
                        'untagged' => (in_array($baseport, $untagged_ids) ? 1 : 0),
                        'port_id' => PortCache::getIdFromIfIndex($this->ifIndexFromBridgePort($baseport), $this->getDeviceId()) ?? 0, // ifIndex from device
                    ]));
                }
            }
        }

        return $ports;
    }
}
