<?php
$state = snmp_get($device, 'DELL-SHADOW-MIB::shadowStatusGlobalStatus.0', '-Oqne');
[$oid, $value] = explode(' ', $state);

if (is_numeric($value)) {
    $descr = 'Global Status';
    $state_name = 'shadowStatusGlobalStatus';
    $states = [
        ['value' => 1, 'generic' => 0, 'graph' => 2, 'descr' => 'other'],
        ['value' => 2, 'generic' => 0, 'graph' => 3, 'descr' => 'unknown'],
        ['value' => 3, 'generic' => 0, 'graph' => 0, 'descr' => 'ok'],
        ['value' => 4, 'generic' => 0, 'graph' => 2, 'descr' => 'critical'],
        ['value' => 5, 'generic' => 0, 'graph' => 2, 'descr' => 'non-Recoverable'],
    ];
    create_state_index($state_name, $states);

    discover_sensor(null, 'state', $device, $oid, 1, $state_name, $descr, 1, 1);
}
