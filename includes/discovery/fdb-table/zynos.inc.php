<?php
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Util\Mac;

if (in_array(explode('-', $device['hardware'], 2)[0], ['GS1900'])) {
    //will match anything starting with GS1900 before the 1st dash (like GS1900-8, GS1900-24E etc etc)
    echo 'Zyxel buggy Q-BRIDGE:' . PHP_EOL;
    // These devices do not provide a proper Q-BRIDGE reply (there is a ".6." index between VLAN and MAC)
    // <vlanid>.6.<mac1>.<mac2>.<mac3>.<mac4>.<mac5>.<mac6>
    // We need to manually handle this here

    $fdbPort_table = snmpwalk_cache_multi_oid($device, 'dot1qTpFdbPort', [], 'Q-BRIDGE-MIB', null, '-OQb');
    foreach ($fdbPort_table as $index => $port_data) {
        // Let's remove the wrong data in the index

        // We'll assume that 1st element is vlan, and last 6 are mac. This will remove the '6' in between them and be safe in case they
        // fix the Q-BRIDGE implementation
        $indexes = explode('.', $index);
        $vlan = $indexes[0]; //1st element
        $mac_address = Mac::parse((string) implode(':', array_map('dechex', array_splice($indexes, -6, 6))))->hex(); //last 6 elements

        $port_id = PortCache::getIdFromIfIndex($port_data['Q-BRIDGE-MIB::dot1qTpFdbPort'], $device['device_id']);

        $vlan_id = isset($vlans_dict[$vlan]) ? $vlans_dict[$vlan] : 0;

        Log::debug("vlan $vlan (id $vlan_id) mac $mac_address port $port_id\n");
        $insert[$vlan_id][$mac_address]['port_id'] = $port_id;
    }
}
