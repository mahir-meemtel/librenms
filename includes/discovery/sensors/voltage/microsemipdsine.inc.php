<?php
$mainVoltage = trim(snmp_get($device, '.1.3.6.1.4.1.7428.1.2.2.1.1.2.1', '-Oqv'), '" ');

if (! empty($mainVoltage)) {
    $divisor = 1;
    $index = '2.1';
    $descr = 'Power Supply Voltage';
    $type = 'microsemipdsine';
    $oid = '.1.3.6.1.4.1.7428.1.2.2.1.1.2.1';
    $current_value = $mainVoltage / $divisor;

    discover_sensor(null, 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current_value);
}
