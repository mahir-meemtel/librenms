<?php
$temp_oid = '1.3.6.1.4.1.2011.2.6.7.1.1.2.1.10.0';
$descr_oid = '1.3.6.1.4.1.2011.2.6.7.1.1.2.1.7.0';

$data = snmpwalk_array_num($device, $temp_oid);
$descr_data = snmpwalk_array_num($device, $descr_oid);

$data = reset($data);
$descr_data = reset($descr_data);

foreach ($data as $index => $value) {
    if ($value < '999') {
        $tempCurr = $value;
        $temperature_oid = '.' . $temp_oid . '.' . $index;
        $descr = $descr_data[$index];
        discover_sensor(null, 'temperature', $device, $temperature_oid, $index, 'smartax', $descr, '1', '1', null, null, null, null, $tempCurr);
    }
}
