<?php
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Util\Mac;

$binding = snmpwalk_group($device, 'agentDynamicDsBindingTable', 'EdgeSwitch-SWITCHING-MIB', 1);

foreach ($binding as $mac => $data) {
    $port_id = PortCache::getIdFromIfIndex($data['agentDynamicDsBindingIfIndex'], $device['device_id']);
    $mac_address = Mac::parse($mac)->hex();
    $vlan_id = $data['agentDynamicDsBindingVlanId'] ?: 0;
    $insert[$vlan_id][$mac_address]['port_id'] = $port_id;
    Log::debug("vlan $vlan_id mac $mac_address port $port_id\n");
}
