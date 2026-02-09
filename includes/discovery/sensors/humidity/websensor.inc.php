<?php
if (is_numeric($pre_cache['websensor_valuesInt']['humInt.0'])) {
    $humidity_oid = '.1.3.6.1.4.1.22626.1.2.3.2.0';
    $humidity_index = 'humInt.0';
    $descr = 'Humidity';
    $humidity = $pre_cache['websensor_valuesInt']['humInt.0'] / 10;
    $high_limit = $pre_cache['websensor_settings']['humHighInt.0'] / 10;
    $low_limit = $pre_cache['websensor_settings']['humLowInt.0'] / 10;
    discover_sensor(null, 'humidity', $device, $humidity_oid, $humidity_index, 'websensor', $descr, '10', '1', $low_limit, null, null, $high_limit, $humidity);
}
