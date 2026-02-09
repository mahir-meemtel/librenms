<?php
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Util\Mac;

$fdbPort_table = snmpwalk_group($device, 'hwDynFdbPort', 'HUAWEI-L2MAM-MIB');
$hwCfgMacAddrQueryIfIndex = snmpwalk_group($device, 'hwCfgMacAddrQueryIfIndex', 'HUAWEI-L2MAM-MIB', 10);

if (! empty($fdbPort_table)) {
    echo 'HUAWEI-L2MAM-MIB:' . PHP_EOL;
    $data_oid = 'hwDynFdbPort';
    // Collect data and populate $insert
    foreach ($fdbPort_table as $mac => $data) {
        foreach ($data[$data_oid] as $vlan => $basePort) {
            $ifIndex = reset($basePort); // $baseport can be ['' => '119'] or ['0' => '119']
            if (! $ifIndex) {
                continue;
            }
            $port_id = PortCache::getIdFromIfIndex($ifIndex, $device['device_id']);
            $mac_address = Mac::parse($mac)->hex();
            if (strlen($mac_address) != 12) {
                Log::debug("MAC address padding failed for $mac\n");
                continue;
            }
            $vlan_id = isset($vlans_dict[$vlan]) ? $vlans_dict[$vlan] : 0;
            $insert[$vlan_id][$mac_address]['port_id'] = $port_id;
            Log::debug("vlan $vlan mac $mac_address port ($ifIndex) $port_id\n");
        }
    }
}

// Static (sticky) mac addresses are not stored in the same table.
if (! empty($hwCfgMacAddrQueryIfIndex)) {
    echo 'HUAWEI-L2MAM-MIB (static):' . PHP_EOL;
    foreach ($hwCfgMacAddrQueryIfIndex as $vlan => $data) {
        if (! empty($data[0][0][0])) {
            foreach ($data[0][0][0] as $mac => $data_next) {
                if (! empty($data_next['showall'][0][0][0][0]['hwCfgMacAddrQueryIfIndex'])) {
                    $basePort = $data_next['showall'][0][0][0][0]['hwCfgMacAddrQueryIfIndex'];
                    $ifIndex = reset($basePort);
                    if (! $ifIndex) {
                        continue;
                    }
                    $port_id = PortCache::getIdFromIfIndex($ifIndex, $device['device_id']);
                    $mac_address = Mac::parse($mac)->hex();
                    if (strlen($mac_address) != 12) {
                        Log::debug("MAC address padding failed for $mac\n");
                        continue;
                    }
                    $vlan_id = isset($vlans_dict[$vlan]) ? $vlans_dict[$vlan] : 0;
                    $insert[$vlan_id][$mac_address]['port_id'] = $port_id;
                    Log::debug("vlan $vlan mac $mac_address port ($ifIndex) $port_id\n");
                }
            }
        }
    }
}

unset($fdbPort_table);
unset($hwCfgMacAddrQueryIfIndex);
