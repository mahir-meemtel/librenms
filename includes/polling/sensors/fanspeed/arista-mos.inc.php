<?php
if ((strpos($sensor['sensor_oid'], '.1.3.6.1.4.1.2021.13.16.3.1.3.') === 0) &&
        ($sensor_value >= 2 ** 31)) {
    // 2's complement negation of the value
    $sensor_value = $sensor_value ^ 0xFFFFFFFF;
    $sensor_value += 1;
}
