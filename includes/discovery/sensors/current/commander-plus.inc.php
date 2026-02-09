<?php
$current = snmp_get($device, 'batteryCurrent.0', '-Oqv', 'CCPOWER-MIB');
$oid = '.1.3.6.1.4.1.18642.1.2.2.1.0';
$descr = 'Battery current';
$divisor = 1;
$multiplier = 1;
discover_sensor(null, 'current', $device, $oid, 'batteryCurrent', 'commander-plus', $descr, $divisor, $multiplier, null, null, null, null, $current);

$current = snmp_get($device, 'rectifierLoadCurrent.0', '-Oqv', 'CCPOWER-MIB');
$oid = '.1.3.6.1.4.1.18642.1.2.1.2.0';
$descr = 'Rectifier Current';
$divisor = 1;
$multiplier = 1;
$limit_low = 0;
$limit = 5000;
discover_sensor(null, 'current', $device, $oid, 'rectifierLoadCurrent', 'commander-plus', $descr, $divisor, $multiplier, $limit_low, null, null, $limit, $current);
