<?php
echo 'coolingUnitStatusAnalogEntry ';
$pre_cache['cooling_unit_analog'] = snmpwalk_cache_oid($device, 'coolingUnitStatusAnalogEntry', [], 'PowerNet-MIB');

echo 'upsPhaseNumInputPhases ';
$pre_cache['apcups_phase_count'] = snmp_get($device, 'upsPhaseNumInputPhases.1', '-OQv', 'PowerNet-MIB');

echo 'memSensorsStatusTable ';
$pre_cache['mem_sensors_status'] = snmpwalk_cache_oid($device, 'memSensorsStatusTable', [], 'PowerNet-MIB', null, '-OQUse');

echo 'memSensorsStatusSysTempUnits ';
$pre_cache['memSensorsStatusSysTempUnits'] = snmp_get($device, 'memSensorsStatusSysTempUnits.0', '-OQv', 'PowerNet-MIB');
