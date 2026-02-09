<?php
$value = snmp_get($device, 'climateVolts', '-Oqv', 'GEIST-MIB-V3');
$current_oid = '.1.3.6.1.4.1.21239.2.2.1.14.1';
$descr = 'Voltage';
if (is_numeric($value)) {
    discover_sensor(null, 'voltage', $device, $current_oid, 'climateVolts', 'geist-watchdog', $descr, 1, 1, null, null, null, null, $value);
}
