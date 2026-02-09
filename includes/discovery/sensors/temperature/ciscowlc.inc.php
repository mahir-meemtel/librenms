<?php
echo ' AIRESPACE temperature ';

$temp = snmpwalk_cache_multi_oid($device, 'bsnSensorTemperature', [], 'AIRESPACE-WIRELESS-MIB');
$low = snmpwalk_cache_multi_oid($device, 'bsnTemperatureAlarmLowLimit', [], 'AIRESPACE-WIRELESS-MIB');
$high = snmpwalk_cache_multi_oid($device, 'bsnTemperatureAlarmHighLimit', [], 'AIRESPACE-WIRELESS-MIB');

if (is_array($temp)) {
    $cur_oid = '.1.3.6.1.4.1.14179.2.3.1.13.';
    foreach ($temp as $index => $entry) {
        $descr = 'Unit Temperature ' . $index;
        echo " $descr, ";
        discover_sensor(null, 'temperature', $device, $cur_oid . $index, $index, 'wlc', $descr, '1', '1', null, $low[$index]['bsnTemperatureAlarmLowLimit'], $high[$index]['bsnTemperatureAlarmHighLimit'], null, $temp[$index]['bsnSensorTemperature'], 'snmp', $index);
    }
}
