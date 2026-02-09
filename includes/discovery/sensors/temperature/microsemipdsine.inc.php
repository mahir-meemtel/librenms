<?php
$temperature_unit = trim(snmp_get($device, '.1.3.6.1.4.1.7428.1.2.2.1.1.12.1', '-Oqv'), '" ');
$temperature = trim(snmp_get($device, '.1.3.6.1.4.1.7428.1.2.2.1.1.11.1', '-Oqv'), '" ');

if (! empty($temperature_unit) && ! empty($temperature)) {
    // If fahrenheit convert to celsius
    $function = null;
    if ($temperature_unit == '2') {
        $function = 'fahrenheit_to_celsius';
        $temperature = fahrenheit_to_celsius($temperature);
    }

    $divisor = 1;
    $index = '11.1';
    $descr = 'Unit Temperature';
    $type = 'microsemipdsine';
    $oid = '.1.3.6.1.4.1.7428.1.2.2.1.1.11.1';
    $current_value = $temperature / $divisor;

    discover_sensor(null, 'temperature', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current_value, 'snmp', null, null, $function);
}
