<?php
$current = snmp_get($device, 'batteryTemperature.0', '-Oqv', 'CCPOWER-MIB');
$oid = '.1.3.6.1.4.1.18642.1.2.2.2.0';
$descr = 'Battery temperature';
$divisor = 1;
$multiplier = 1;
discover_sensor(null, 'temperature', $device, $oid, 'batteryTemperature', 'commander-plus', $descr, $divisor, $multiplier, null, null, null, null, $current);
