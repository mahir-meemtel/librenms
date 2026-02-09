<?php
echo 'Accedian MetroNID';

// Chassis temperature
$high_limit = 90;
$high_warn_limit = 85;
$low_warn_limit = 5;
$low_limit = 1;

$descroid = '.1.3.6.1.4.1.22420.1.1.12.1.7.1'; // acdDescTsEntry.7.1
$descr = snmp_get($device, $descroid, '-Oqv');
$descr = str_replace('"', '', $descr);
$valueoid = '.1.3.6.1.4.1.22420.1.1.12.1.2.1'; // acdDescTsCurrentTemp.1
$value = snmp_get($device, $valueoid, '-Oqv');

if (is_numeric($value)) {
    discover_sensor(null, 'temperature', $device, $valueoid, 1, 'metronid', $descr, '1', '1', $low_limit, $low_warn_limit, $high_warn_limit, $high_limit, $value);
}
