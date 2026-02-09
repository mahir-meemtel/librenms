<?php
$tmp_eltex = snmp_get_multi_oid($device, 'ltp8xSensor1Temperature.0 ltp8xSensor2Temperature.0 ltp8xSensor1TemperatureExt.0 ltp8xSensor2TemperatureExt.0', '-OUQn', 'ELTEX-LTP8X-STANDALONE');

if (isset($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.10.0']) && is_numeric($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.10.0'])) {
    $oid = '.1.3.6.1.4.1.35265.1.22.1.10.10.0';
    $index = 'ltp8xSensor1Temperature';
    $type = 'eltex-olt';
    $descr = 'Sensor 1 Temp';
    $divisor = 1;
    $current = $tmp_eltex[$oid];
    discover_sensor(null, 'temperature', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}

if (isset($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.11.0']) && is_numeric($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.11.0'])) {
    $oid = '.1.3.6.1.4.1.35265.1.22.1.10.11.0';
    $index = 'ltp8xSensor2Temperature';
    $type = 'eltex-olt';
    $descr = 'Sensor 2 Temp';
    $divisor = 1;
    $current = $tmp_eltex[$oid];
    discover_sensor(null, 'temperature', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}

if (isset($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.12.0']) && is_numeric($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.12.0']) && $tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.12.0'] != 65535) {
    $oid = '.1.3.6.1.4.1.35265.1.22.1.10.12.0';
    $index = 'ltp8xSensor1TemperatureExt';
    $type = 'eltex-olt';
    $descr = 'Sensor 1 External Temp';
    $divisor = 1;
    $current = $tmp_eltex[$oid];
    discover_sensor(null, 'temperature', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}

if (isset($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.13.0']) && is_numeric($tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.13.0']) && $tmp_eltex['.1.3.6.1.4.1.35265.1.22.1.10.13.0'] != 65535) {
    $oid = '.1.3.6.1.4.1.35265.1.22.1.10.13.0';
    $index = 'ltp8xSensor2TemperatureExt';
    $type = 'eltex-olt';
    $descr = 'Sensor 2 External Temp';
    $divisor = 1;
    $current = $tmp_eltex[$oid];
    discover_sensor(null, 'temperature', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}

unset($tmp_eltex);
