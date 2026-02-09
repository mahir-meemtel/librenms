<?php
$systemVoltage = trim(snmp_get($device, 'systemVoltage.0', '-Oqv', 'ICT-DISTRIBUTION-PANEL-MIB'), '" ');

if (! empty($systemVoltage)) {
    $divisor = 1;
    $oid = '.1.3.6.1.4.1.39145.10.6.0';
    $index = 0;
    $descr = 'System Voltage';
    $type = 'ict-pdu';
    $current_value = $systemVoltage / $divisor;

    discover_sensor(null, 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current_value);
}
