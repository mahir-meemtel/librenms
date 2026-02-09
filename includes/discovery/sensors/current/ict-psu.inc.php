<?php
$outputCurrent = trim(snmp_get($device, 'outputCurrent.0', '-Oqv', 'ICT-DIGITAL-SERIES-MIB'), '" ');
if (! empty($outputCurrent)) {
    $divisor = 1;
    $index = 0;
    $oid = '.1.3.6.1.4.1.39145.11.8.0';
    $descr = 'Output Current';
    $type = 'ict-psu';
    $currentValue = $outputCurrent / $divisor;

    discover_sensor(null, 'current', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $currentValue);
}
