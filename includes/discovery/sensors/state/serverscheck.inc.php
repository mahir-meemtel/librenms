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

foreach ($pre_cache['serverscheck_control'] as $oid_name => $oid_value) {
    if (Str::contains($oid_name, 'name') && Str::contains($oid_value, ['Flooding', 'Leckage'])) {
        preg_match("/(\d+)/", $oid_name, $temp_x);
        $tmp_oid = 'sensor' . $temp_x[0] . 'Value.0';
        $current = $pre_cache['serverscheck_control'][$tmp_oid];
        $state_name = 'Serverscheck_FloodSensor';
        if ($current) {
            $index = str_replace('.0', '', $oid_name);
            $descr = $oid_value;
            $states = [
                ['value' => 1, 'generic' => 1, 'graph' => 1, 'descr' => '-'],
                ['value' => 2, 'generic' => 0, 'graph' => 1, 'descr' => 'DRY'],
                ['value' => 4, 'generic' => 2, 'graph' => 1, 'descr' => 'WET'],
            ];
            create_state_index($state_name, $states);

            discover_sensor(null, 'state', $device, $serverscheck_oids[$tmp_oid], $index, $state_name, $descr, 1, 1, null, null, null, null, 1);
        }
    }
}
