<?php
if ($sensor['sensor_type'] === 'dhcpatriotLicenseExpiration') {
    $current_time = time();
    $epoch_time = explode(':', $sensor_value);
    $sensor_value = round((intval($epoch_time[1]) - $current_time) / (60 * 60 * 24));
}
