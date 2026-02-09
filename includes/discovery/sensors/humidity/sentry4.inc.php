<?php
foreach ($pre_cache['sentry4_humid'] as $index => $data) {
    $descr = $data['st4HumidSensorName'];
    $oid = ".1.3.6.1.4.1.1718.4.1.10.3.1.1.$index";
    $low_limit = $data['st4HumidSensorLowAlarm'];
    $low_warn_limit = $data['st4HumidSensorLowWarning'];
    $high_limit = $data['st4HumidSensorHighAlarm'];
    $high_warn_limit = $data['st4HumidSensorHighWarning'];
    $current = $data['st4HumidSensorValue'];
    if ($current >= 0) {
        discover_sensor(null, 'humidity', $device, $oid, "st4HumidSensorValue.$index", 'sentry4', $descr, 1, 1, $low_limit, $low_warn_limit, $high_warn_limit, $high_limit, $current);
    }
}
