<?php
$temp = snmpwalk_cache_multi_oid($device, 'trpzSysPowerSupplyTable', [], 'TRAPEZE-NETWORKS-SYSTEM-MIB');
$cur_oid = '.1.3.6.1.4.1.14525.4.8.1.1.13.1.2.1.2.';

if (is_array($temp)) {
    //Create State Index
    $state_name = 'trpzSysPowerSupplyStatus';
    $states = [
        ['value' => 1, 'generic' => 1, 'graph' => 0, 'descr' => 'other'],
        ['value' => 2, 'generic' => 3, 'graph' => 0, 'descr' => 'unknown'],
        ['value' => 3, 'generic' => 2, 'graph' => 0, 'descr' => 'ac-failed'],
        ['value' => 4, 'generic' => 2, 'graph' => 0, 'descr' => 'dc-failed'],
        ['value' => 5, 'generic' => 0, 'graph' => 0, 'descr' => 'ac-ok-dc-ok'],
    ];
    create_state_index($state_name, $states);

    foreach ($temp as $index => $entry) {
        $descr = $temp[$index]['trpzSysPowerSupplyDescr'];
        //Discover Sensors
        discover_sensor(null, 'state', $device, $cur_oid . $index, $index, $state_name, $descr, 1, 1, null, null, null, null, $temp[$index]['trpzSysPowerSupplyStatus'], 'snmp', $index);
    }
}
