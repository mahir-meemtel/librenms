<?php
if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.738.1.5.100') !== false) {
    echo 'Telco Systems:';

    // CPU temperature
    $high_limit = 70;
    $high_warn_limit = 65;
    $low_warn_limit = 5;
    $low_limit = 0;

    $descr = 'CPU Temperature';
    $valueoid = '.1.3.6.1.4.1.738.1.5.100.3.2.3.0'; // PRVT-SWITCH-MIB::reportsHardwareTemperature.0
    $value = snmp_get($device, $valueoid, '-Oqv');
    $value = str_replace('"', '', $value);

    if (is_numeric($value)) {
        discover_sensor(null, 'temperature', $device, $valueoid, 1, 'binos', $descr, '1', '1', $low_limit, $low_warn_limit, $high_warn_limit, $high_limit, $value);
    }
}
