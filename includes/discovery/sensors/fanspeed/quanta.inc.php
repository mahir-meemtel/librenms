<?php
d_echo('Quanta Fan Speeds');
$sensor_type = 'quanta_fan';
//FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanSpeed
$sensors_id_oid = 'boxServicesFanSpeed';
$sensors_values = snmpwalk_cache_multi_oid($device, $sensors_id_oid, [], 'NETGEAR-BOXSERVICES-PRIVATE-MIB');
$numeric_oid_base = '.1.3.6.1.4.1.4413.1.1.43.1.6.1.4';

foreach ($sensors_values as $index => $entry) {
    $current_value = $entry[$sensors_id_oid];
    $descr = "Fan Speed $index:";

    if ($current_value > 0) {
        discover_sensor(null, 'fanspeed', $device, "$numeric_oid_base.$index", $index, $sensor_type, $descr, 1, 1, null, null, null, null, $current_value);
    }
}
