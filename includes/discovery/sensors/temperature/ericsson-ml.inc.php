<?php
d_echo('ERICSSON-ML');
$oid = '.1.3.6.1.4.1.193.223.2.4.1.1.2.1';
$index = 0;
$sensor_type = 'temperatureRadio';
$descr = 'Internal Temp';
$divisor = 1;
$temperature = (float) snmp_get($device, $oid, '-Oqv', 'PT-MONITOR-MIB');

if ($temperature != 0.0) {
    discover_sensor(null, 'temperature', $device, $oid, $index, $sensor_type, $descr, $divisor, 1, null, null, null, null, $temperature);
}
