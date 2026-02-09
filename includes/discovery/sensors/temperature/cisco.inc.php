<?php
$temp = snmpwalk_cache_multi_oid($device, 'ciscoEnvMonTemperatureStatusTable', [], 'CISCO-ENVMON-MIB');
if (is_array($temp)) {
    $cur_oid = '.1.3.6.1.4.1.9.9.13.1.3.1.3.';
    foreach ($temp as $index => $entry) {
        if (! isset($temp[$index]['ciscoEnvMonTemperatureStatusValue'])) {
            continue;
        }
        if ($temp[$index]['ciscoEnvMonTemperatureState'] != 'notPresent' && ! empty($temp[$index]['ciscoEnvMonTemperatureStatusDescr'])) {
            $descr = ucwords($temp[$index]['ciscoEnvMonTemperatureStatusDescr']);
            discover_sensor(null, 'temperature', $device, $cur_oid . $index, $index, 'cisco', $descr, '1', '1', null, null, null, $temp[$index]['ciscoEnvMonTemperatureThreshold'], $temp[$index]['ciscoEnvMonTemperatureStatusValue'], 'snmp', $index);
        }
    }
}

$temp = snmpwalk_cache_multi_oid($device, 'c3gModemTemperature', [], 'CISCO-WAN-3G-MIB');
if (is_array($temp)) {
    $cur_oid = '.1.3.6.1.4.1.9.9.661.1.1.1.12.';
    foreach ($temp as $index => $entry) {
        $descr = snmp_get($device, 'entPhysicalName.' . $index, '-Oqv', 'ENTITY-MIB');
        discover_sensor(null, 'temperature', $device, $cur_oid . $index, $index, 'cisco', $descr, '1', '1', null, null, null, $temp[$index]['ciscoEnvMonTemperatureThreshold'] ?? null, $temp[$index]['c3gModemTemperature'], 'snmp', $index);
    }
}
