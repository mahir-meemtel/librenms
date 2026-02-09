<?php
$tpdin_oids = [
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.9.0',
        'index' => 'current1',
        'descr' => 'Current 1',
        'current' => $pre_cache['tpdin_monitor'][0]['current1'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.10.0',
        'index' => 'current2',
        'descr' => 'Current 2',
        'current' => $pre_cache['tpdin_monitor'][0]['current2'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.11.0',
        'index' => 'current3',
        'descr' => 'Current 3',
        'current' => $pre_cache['tpdin_monitor'][0]['current3'],
    ],
    [
        'oid' => '.1.3.6.1.4.1.45621.2.2.12.0',
        'index' => 'current4',
        'descr' => 'Current 4',
        'current' => $pre_cache['tpdin_monitor'][0]['current4'],
    ],
];

foreach ($tpdin_oids as $data) {
    if ($data['current'] > 0) {
        discover_sensor(null, 'current', $device, $data['oid'], $data['index'], $device['os'], $data['descr'], 10, '1', null, null, null, null, $data['current']);
    }
}

unset($tpdin_oids);
