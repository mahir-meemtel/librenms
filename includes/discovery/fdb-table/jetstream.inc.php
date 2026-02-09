<?php
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Util\Mac;

$oids = SnmpQuery::allowUnordered()->hideMib()->walk('Q-BRIDGE-MIB::dot1qTpFdbPort')->table(2);
if (! empty($oids)) {
    $insert = [];
    Log::debug('Jetstream: FDB Table');
    foreach ($oids as $vlan => $oidData) {
        foreach ($oidData as $mac => $macData) {
            $port = $macData['dot1qTpFdbPort'];
            //try both variation with & without space
            $port_id = find_port_id('gigabitEthernet 1/0/' . $port, 'gigabitEthernet1/0/' . $port, $device['device_id']) ?? 0;
            $mac_address = Mac::parse($mac)->hex();
            if (strlen($mac_address) != 12) {
                Log::debug("MAC address padding failed for $mac\n");
                continue;
            }
            $vlan_id = $vlans_dict[$vlan] ?? 0;
            $insert[$vlan_id][$mac_address]['port_id'] = $port_id;
            Log::debug("vlan $vlan_id mac $mac_address port $port_id\n");
        }
    }
}

echo PHP_EOL;
