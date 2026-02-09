<?php
$power_frame_oid = '.1.3.6.1.4.1.2011.2.6.7.1.1.1.1.11.0';

$power = snmp_get($device, $power_frame_oid, '-Ovq');
$index = '0';

discover_sensor(null, 'power', $device, $power_frame_oid, $index, 'smartax-total', 'Chassis Total', '1', '1', null, null, null, null, $power);

$power_oid = '1.3.6.1.4.1.2011.2.6.7.1.1.2.1.11.0';
$descr_oid = '1.3.6.1.4.1.2011.2.6.7.1.1.2.1.7.0';

$data = snmpwalk_array_num($device, $power_oid);
$descr_data = snmpwalk_array_num($device, $descr_oid);

$data = reset($data);
$descr_data = reset($descr_data);

foreach ($data as $index => $value) {
    $powerCurr = $value;
    $pow_oid = '.' . $power_oid . '.' . $index;
    $descr = $descr_data[$index];
    discover_sensor(null, 'power', $device, $pow_oid, $index, 'smartax', $descr, '1', '1', null, null, null, null, $powerCurr);
}
