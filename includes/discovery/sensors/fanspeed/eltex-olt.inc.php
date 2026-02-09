<?php
$tmp_eltex = snmp_get_multi_oid($device, 'ltp8xFan0Active.0 ltp8xFan0RPM.0 ltp8xFan1Active.0 ltp8xFan1RPM.0 ltp8xFanMinRPM.0 ltp8xFanMaxRPM.0', '-OUQn', 'ELTEX-LTP8X-STANDALONE');

$min_eltex = $tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.20.0'] ?? null;
$max_eltex = $tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.21.0'] ?? null;

if (isset($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.6.0'])) {
    if (is_numeric($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.7.0'])) {
        $oid = '.1.3.6.1.4.1.35265.1.22.1.10.7.0';
        $index = 0;
        $type = 'eltex-olt';
        $descr = 'Fan 0';
        $divisor = 1;
        $fanspeed = $tmp_eltex[$oid];
        discover_sensor(null, 'fanspeed', $device, $oid, $index, $type, $descr, $divisor, '1', $min_eltex, null, null, $max_eltex, $fanspeed);
    }
}

if (isset($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.8.0'])) {
    if (is_numeric($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.9.0'])) {
        $oid = '.1.3.6.1.4.1.35265.1.22.1.10.9.0';
        $index = 1;
        $type = 'eltex-olt';
        $descr = 'Fan 1';
        $divisor = 1;
        $fanspeed = $tmp_eltex[$oid];
        discover_sensor(null, 'fanspeed', $device, $oid, $index, $type, $descr, $divisor, '1', $min_eltex, null, null, $max_eltex, $fanspeed);
    }
}

unset($tmp_eltex);
