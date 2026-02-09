<?php
$tpdin_oids = [
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.5.0',
        'index' => 'voltage1',
        'descr' => 'Voltage 1',
        'current' => $pre_cache['tpdin_monitor'][0]['voltage1'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.6.0',
        'index' => 'voltage2',
        'descr' => 'Voltage 2',
        'current' => $pre_cache['tpdin_monitor'][0]['voltage2'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.7.0',
        'index' => 'voltage3',
        'descr' => 'Voltage 3',
        'current' => $pre_cache['tpdin_monitor'][0]['voltage3'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.8.0',
        'index' => 'voltage4',
        'descr' => 'Voltage 4',
        'current' => $pre_cache['tpdin_monitor'][0]['voltage4'],
    ],
];

foreach ($tpdin_oids as $data) {
    if ($data['current'] > 0) {
        discover_sensor(null, 'voltage', $device, $data['oid'], $data['index'], $device['os'], $data['descr'], 10, '1', null, null, null, null, $data['current']);
    }
}

unset($tpdin_oids);
