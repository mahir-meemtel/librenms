<?php
echo 'st4HumidSensorConfigTable ';
$pre_cache['sentry4_humid'] = snmpwalk_cache_oid($device, 'st4HumidSensorConfigTable', [], 'Sentry4-MIB');

echo 'st4HumidSensorMonitorTable ';
$pre_cache['sentry4_humid'] = snmpwalk_cache_oid($device, 'st4HumidSensorMonitorTable', $pre_cache['sentry4_humid'], 'Sentry4-MIB');

echo 'st4HumidSensorEventConfigTable ';
$pre_cache['sentry4_humid'] = snmpwalk_cache_oid($device, 'st4HumidSensorEventConfigTable', $pre_cache['sentry4_humid'], 'Sentry4-MIB');

echo 'st4TempSensorConfigTable ';
$pre_cache['sentry4_temp'] = snmpwalk_cache_oid($device, 'st4TempSensorConfigTable', [], 'Sentry4-MIB');

echo 'st4TempSensorMonitorTable ';
$pre_cache['sentry4_temp'] = snmpwalk_cache_oid($device, 'st4TempSensorMonitorTable', $pre_cache['sentry4_temp'], 'Sentry4-MIB');

echo 'st4TempSensorEventConfigTable ';
$pre_cache['sentry4_temp'] = snmpwalk_cache_oid($device, 'st4TempSensorEventConfigTable', $pre_cache['sentry4_temp'], 'Sentry4-MIB');

echo 'st4InputCordConfigTable ';
$pre_cache['sentry4_input'] = snmpwalk_cache_oid($device, 'st4InputCordConfigTable', [], 'Sentry4-MIB');

echo 'st4InputCordMonitorTable ';
$pre_cache['sentry4_input'] = snmpwalk_cache_oid($device, 'st4InputCordMonitorTable', $pre_cache['sentry4_input'], 'Sentry4-MIB');

echo 'st4InputCordEventConfigTable ';
$pre_cache['sentry4_input'] = snmpwalk_cache_oid($device, 'st4InputCordEventConfigTable', $pre_cache['sentry4_input'], 'Sentry4-MIB');
