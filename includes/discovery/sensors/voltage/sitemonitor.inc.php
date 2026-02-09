<?php
$oid = '.1.3.6.1.4.1.32050.2.1.27.5.1';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
discover_sensor(null, 'voltage', $device, $oid, 1, 'sitemonitor', 'Shunt Input', 10, 1, null, null, null, null, $current);

$oid = '.1.3.6.1.4.1.32050.2.1.27.5.2';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
discover_sensor(null, 'voltage', $device, $oid, 2, 'sitemonitor', 'Power 1', 10, 1, null, null, null, null, $current);

$oid = '.1.3.6.1.4.1.32050.2.1.27.5.3';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
discover_sensor(null, 'voltage', $device, $oid, 3, 'sitemonitor', 'Power 2', 10, 1, null, null, null, null, $current);
