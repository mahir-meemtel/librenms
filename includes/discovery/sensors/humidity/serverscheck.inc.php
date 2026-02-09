<?php
use Illuminate\Support\Str;

$serverscheck_oids = [
    'sensor1Value.0' => '.1.3.6.1.4.1.17095.3.2.0',
    'sensor2Value.0' => '.1.3.6.1.4.1.17095.3.6.0',
    'sensor3Value.0' => '.1.3.6.1.4.1.17095.3.10.0',
    'sensor4Value.0' => '.1.3.6.1.4.1.17095.3.14.0',
    'sensor5Value.0' => '.1.3.6.1.4.1.17095.3.18.0',
    'sensor6Value.0' => '.1.3.6.1.4.1.17095.3.22.0',
    'sensor7Value.0' => '.1.3.6.1.4.1.17095.3.26.0',
    'sensor8Value.0' => '.1.3.6.1.4.1.17095.3.30.0',
];

$temp_x = 1;
foreach ($pre_cache['serverscheck_control'] as $oid_name => $oid_value) {
    if (Str::contains($oid_name, 'name')) {
        $tmp_oid = 'sensor' . $temp_x . 'Value.0';
        $current = $pre_cache['serverscheck_control'][$tmp_oid];
        if (Str::contains($oid_value, 'Humid')) {
            if (is_numeric($current)) {
                $index = str_replace('.0', '', $oid_name);
                $descr = $oid_value;
                discover_sensor(null, 'humidity', $device, $serverscheck_oids[$tmp_oid], $index, 'serverscheck', $descr, 1, 1, null, null, null, null, $current);
            }
        }
        $temp_x++;
    }
}
