<?php
$oid = '.1.3.6.1.4.1.32050.2.1.27.5.0';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
discover_sensor(null, 'temperature', $device, $oid, 0, 'sitemonitor', 'Temperature', 10, 1, null, null, null, null, $current);

$oid = '.1.3.6.1.4.1.32050.2.1.27.5.5';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
discover_sensor(null, 'temperature', $device, $oid, 5, 'sitemonitor', 'Relay on Above', 10, 1, null, null, null, null, $current);

$oid = '.1.3.6.1.4.1.32050.2.1.27.5.6';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
discover_sensor(null, 'temperature', $device, $oid, 6, 'sitemonitor', 'Relay on Below', 10, 1, null, null, null, null, $current);
