<?php
$inputVoltage = trim(snmp_get($device, 'inputVoltage.0', '-Oqv', 'ICT-DIGITAL-SERIES-MIB'), '" ');
if (! empty($inputVoltage)) {
    $divisor = 1;
    $index = 0;
    $oid = '.1.3.6.1.4.1.39145.11.6.0';
    $descr = 'Input Voltage';
    $type = 'ict-psu';
    $currentValue = $inputVoltage / $divisor;
    echo "got in\n";
    discover_sensor(null, 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $currentValue);
}

// Output Voltage
// SNMPv2-SMI::enterprises.39145.11.7.0 = STRING: "55.2" -- outputVoltage

$outputVoltage = trim(snmp_get($device, 'outputVoltage.0', '-Oqv', 'ICT-DIGITAL-SERIES-MIB'), '" ');
if (! empty($outputVoltage)) {
    $divisor = 1;
    $index = 1;
    $oid = '.1.3.6.1.4.1.39145.11.7.0';
    $descr = 'Output Voltage';
    $type = 'ict-psu';
    $currentValue = $outputVoltage / $divisor;

    discover_sensor(null, 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $currentValue);
}
