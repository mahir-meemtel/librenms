<?php
$switch = snmp_get($device, '.1.3.6.1.4.1.32050.2.1.26.5.3', '-Ovqe');

if ($switch) {
    //Create State Index
    $state_name = 'switchInput';
    $states = [
        ['value' => 0, 'generic' => 1, 'graph' => 1, 'descr' => 'Open'],
        ['value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'Closed'],
    ];
    create_state_index($state_name, $states);

    $sensor_index = 3;
    discover_sensor(
        null,
        'state',
        $device,
        '.1.3.6.1.4.1.32050.2.1.26.5.3',
        $sensor_index,
        $state_name,
        'Switch Input',
        1,
        1,
        null,
        null,
        null,
        null
    );
}
