<?php
$divisor = '10';
$multiplier = '1';

d_echo($pre_cache['sentry4_temp']);
$sentry_temp_scale = snmp_get($device, 'st4TempSensorScale.0', '-Ovq', 'Sentry4-MIB');
foreach ($pre_cache['sentry4_temp'] as $index => $data) {
    $descr = $data['st4TempSensorName'];
    $oid = ".1.3.6.1.4.1.1718.4.1.9.3.1.1.$index";
    $low_limit = $data['st4TempSensorLowAlarm'];
    $low_warn_limit = $data['st4TempSensorLowWarning'];
    $high_limit = $data['st4TempSensorHighAlarm'];
    $high_warn_limit = $data['st4TempSensorHighWarning'];
    $current = ($data['st4TempSensorValue'] / $divisor);
    $user_func = null;
    if ($sentry_temp_scale == 'fahrenheit') {
        $low_warn_limit = fahrenheit_to_celsius($low_warn_limit, $sentry_temp_scale);
        $low_limit = fahrenheit_to_celsius($low_limit, $sentry_temp_scale);
        $high_warn_limit = fahrenheit_to_celsius($high_warn_limit, $sentry_temp_scale);
        $high_limit = fahrenheit_to_celsius($high_limit, $sentry_temp_scale);
        $current = fahrenheit_to_celsius($current, $sentry_temp_scale);
        $user_func = 'fahrenheit_to_celsius';
    }
    if (is_numeric($current) && $current >= 0) {
        discover_sensor(null, 'temperature', $device, $oid, 'st4TempSensorValue' . $index, 'sentry4', $descr, $divisor, $multiplier, $low_limit, $low_warn_limit, $high_warn_limit, $high_limit, $current, 'snmp', null, null, $user_func);
    }
}
