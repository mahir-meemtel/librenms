<?php
$oid = '.1.3.6.1.4.1.18642.1.2.1.1.0';
$descr = 'Rectifier Voltage';
$divisor = 10;
$multiplier = 1;
$limit_low = 24;
$limit = 57;
$current = snmp_get($device, 'rectifierFloatVoltage.0', '-Oqv', 'CCPOWER-MIB');
discover_sensor(null, 'voltage', $device, $oid, 'rectifierFloatVoltage', 'commander-plus', $descr, $divisor, $multiplier, $limit_low, null, null, $limit, $current);
