<?php
$start_oid = '.1.3.6.1.4.1.18642.1.2.4';
$state_table = snmpwalk_cache_oid($device, '.1.3.6.1.4.1.18642.1.2.4', [], 'CCPOWER-MIB');
$x = 1;
foreach ($state_table[0] as $state_name => $state_value) {
    //Create State Translation
    $states = [
        ['value' => 1, 'generic' => 2, 'graph' => 1, 'descr' => 'inactive'],
        ['value' => 2, 'generic' => 0, 'graph' => 1, 'descr' => 'active'],
    ];
    create_state_index($state_name, $states);

    $descr = $state_name;
    discover_sensor(null, 'state', $device, $start_oid . '.' . $x . '.0', $state_name, $state_name, $descr, 1, 1, null, null, null, null, $state_value, 'snmp');
    $x++;
}

unset($state_table, $start_oid);
