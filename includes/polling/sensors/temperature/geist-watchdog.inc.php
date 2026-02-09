<?php
if ($sensor_cache['geist_temp_unit'] === '0') {
    $sensor_value = fahrenheit_to_celsius($sensor_value / 10) * 10;
}
