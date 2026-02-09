<?php
$value = snmp_get($device, 'climateAirflow', '-Oqv', 'GEIST-MIB-V3');
$current_oid = '.1.3.6.1.4.1.21239.2.2.1.9.1';
$descr = 'Airflow';
if (is_numeric($value)) {
    discover_sensor(null, 'airflow', $device, $current_oid, 'climateAirflow', 'geist-watchdog', $descr, 1, 1, null, null, null, null, $value);
}
