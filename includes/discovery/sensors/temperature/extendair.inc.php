<?php
$oid = '.1.3.6.1.4.1.25651.1.2.4.2.4.1.3.0';
$index = 0;
$descr = 'Internal temp (far end radio)';
$value = snmp_get($device, 'remCurrentTemp.0', '-Oqv', 'ExaltComProducts');
if ($value) {
    discover_sensor(null, 'temperature', $device, $oid, $index, 'extendair', $descr, '1', '1', null, null, null, null, $value);
}

$oid = '.1.3.6.1.4.1.25651.1.2.4.2.3.1.3.0';
$index = 1;
$descr = 'Internal temp (local radio)';
$value = snmp_get($device, 'locCurrentTemp.0', '-Oqv', 'ExaltComProducts');
if ($value) {
    discover_sensor(null, 'temperature', $device, $oid, $index, 'extendair', $descr, '1', '1', null, null, null, null, $value);
}

unset(
    $oid,
    $index,
    $descr,
    $value
);
