<?php
$power1 = snmp_get($device, 'power1State.0', '-Ovqe', 'OAP-NMU');
$power2 = snmp_get($device, 'power2State.0', '-Ovqe', 'OAP-NMU');
$fan = snmp_get($device, 'fanState.0', '-Ovqe', 'OAP-NMU');
$oid_power1 = '.1.3.6.1.4.1.40989.10.16.20.11.0';
$oid_power2 = '.1.3.6.1.4.1.40989.10.16.20.12.0';
$oid_fan = '.1.3.6.1.4.1.40989.10.16.20.10.0';
$index = '0';

// Power 1 State
if (is_numeric($power1)) {
    $state_name = 'power1State';
    $states = [
        ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'off'],
        ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'on'],
    ];
    create_state_index($state_name, $states);

    $descr = 'Power 1 State';
    discover_sensor(null, 'state', $device, $oid_power1, $index, $state_name, $descr, 1, 1, null, null, null, null, $power1, 'snmp', $index);
}

// Power 2 State
if (is_numeric($power2)) {
    $state_name = 'power2State';
    $states = [
        ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'off'],
        ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'on'],
    ];
    create_state_index($state_name, $states);

    $descr = 'Power 2 State';
    discover_sensor(null, 'state', $device, $oid_power2, $index, $state_name, $descr, 1, 1, null, null, null, null, $power2, 'snmp', $index);
}

// Fan State
if (is_numeric($fan)) {
    $state_name = 'fanState';
    $states = [
        ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'off'],
        ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'on'],
    ];
    create_state_index($state_name, $states);

    $descr = 'Fan State';
    discover_sensor(null, 'state', $device, $oid_fan, $index, $state_name, $descr, 1, 1, null, null, null, null, $fan, 'snmp', $index);
}
