<?php
$tpdin_oids = [
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.13.0',
        'index' => 'temperature1',
        'descr' => 'External Temp',
        'current' => $pre_cache['tpdin_monitor'][0]['temperature1'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.14.0',
        'index' => 'temperature2',
        'descr' => 'Internal Temp',
        'current' => $pre_cache['tpdin_monitor'][0]['temperature2'],
    ],
];

foreach ($tpdin_oids as $data) {
    if ($data['current'] > 0) {
        discover_sensor(null, 'temperature', $device, $data['oid'], $data['index'], $device['os'], $data['descr'], 10, '1', null, null, null, null, $data['current']);
    }
}

unset($tpdin_oids);
