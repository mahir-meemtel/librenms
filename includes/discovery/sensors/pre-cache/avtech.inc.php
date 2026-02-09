<?php
$virtual_tables = [
    'ra32-analog' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.8\.1\.1\.5\.((\d+)\.0)/',
    'ra32-relay' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.8\.1\.1\.6\.((\d+)\.0)/',
    'ra32-ext-temp' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.8\.1\.2\.((\d+)\.1\.0)/',
    'ra32-switch' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.8\.1\.3\.((\d+)\.0)/',
    'ra32-wish-temp' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.8\.1\.4\.((\d+)\.4\.1\.2\.0)/',
    'ra32s-analog' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.11\.1\.1\.5\.((\d+)\.0)/',
    'ra32s-relay' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.11\.1\.1\.6\.((\d+)\.0)/',
    'ra32s-ext-temp' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.11\.1\.2\.((\d+)\.1\.0)/',
    'ra32s-switch' => '/\.1\.3\.6\.1\.4\.1\.20916\.1\.11\.1\.3\.((\d+)\.0)/',
];

$data = trim(snmp_walk($device, '.1.3.6.1.4.1.20916.1', '-OQn'));
foreach (explode(PHP_EOL, $data) as $line) {
    [$oid, $value] = explode(' =', $line);
    $value = trim($value);

    $processed = false;
    foreach ($virtual_tables as $vt_name => $vt_regex) {
        if (preg_match($vt_regex, $oid, $matches)) {
            $index = $matches[1];
            $id = $matches[2];

            $pre_cache[$vt_name][$index] = ['value' => $value, 'id' => $id];

            $processed = true;
            break;  // skip rest
        }
    }

    if (! $processed) {
        $pre_cache[$oid] = [[$oid => $value]];
    }
}

unset($data);
