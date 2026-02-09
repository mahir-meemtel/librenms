<?php
$lib_data = snmpwalk_cache_oid($device, 'lgpEnvTemperatureEntryDegC', [], 'LIEBERT-GP-ENVIRONMENTAL-MIB');

foreach ($lib_data as $index => $data) {
    if (isset($data['lgpEnvTemperatureMeasurementTenthsDegC']) && is_numeric($data['lgpEnvTemperatureMeasurementTenthsDegC'])) {
        $oid = '.1.3.6.1.4.1.476.1.42.3.4.1.3.3.1.50.' . $index;
        $low_limit = $data['lgpEnvTemperatureLowThresholdTenthsDegC'];
        $high_limit = $data['lgpEnvTemperatureHighThresholdTenthsDegC'];
        $current = $data['lgpEnvTemperatureMeasurementTenthsDegC'];
        $divisor = 10;
        $new_index = 'lgpEnvTemperatureMeasurementTenthsDegC.' . $index;
    } elseif (isset($data['lgpEnvTemperatureMeasurementDegC']) && is_numeric($data['lgpEnvTemperatureMeasurementDegC'])) {
        $oid = '.1.3.6.1.4.1.476.1.42.3.4.1.3.3.1.3.' . $index;
        $low_limit = $data['lgpEnvTemperatureLowThresholdDegC'] ?? null;
        $high_limit = $data['lgpEnvTemperatureHighThresholdDegC'] ?? null;
        $current = $data['lgpEnvTemperatureMeasurementDegC'];
        $divisor = 1;
        $new_index = 'lgpEnvTemperatureDescrDegC.' . $index;
    }
    if (is_numeric($current)) {
        $descr = $data['lgpEnvTemperatureDescrDegC'];
        discover_sensor(null, 'temperature', $device, $oid, $new_index, 'liebert', $descr, $divisor, 1, $low_limit, null, null, $high_limit, $current / $divisor);
        unset($current);
    }
}

unset(
    $lib_data,
    $current,
    $oid,
    $descr,
    $low_limit,
    $high_limit,
    $divisor,
    $new_index
);

$return_temp = snmp_get($device, 'lgpEnvReturnAirTemperature.0', '-Oqv');
if (is_numeric($return_temp)) {
    $oid = '.1.3.6.1.4.1.476.1.42.3.4.1.1.2.0';
    $index = 'lgpEnvReturnAirTemperature.0';
    $descr = 'Return Air Temp';
    $divisor = 1;
    discover_sensor(null, 'temperature', $device, $oid, $index, 'liebert', $descr, $divisor, '1', null, null, null, null, $return_temp);
}

$supply_temp = snmp_get($device, 'lgpEnvSupplyAirTemperature.0', '-Oqv');
if (is_numeric($supply_temp)) {
    $oid = '.1.3.6.1.4.1.476.1.42.3.4.1.1.3.0';
    $index = 'lgpEnvSupplyAirTemperature.0';
    $descr = 'Supply Air Temp';
    $divisor = 1;
    discover_sensor(null, 'temperature', $device, $oid, $index, 'liebert', $descr, $divisor, '1', null, null, null, null, $supply_temp);
}
