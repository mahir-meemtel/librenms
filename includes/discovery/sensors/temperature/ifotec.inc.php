<?php
$index = 0;
foreach ($pre_cache['ifoTemperatureTable'] ?? [] as $ifoSensor) {
    discover_sensor(
        null,
        'temperature',
        $device,
        $ifoSensor['ifoTempValue']['oid'],
        $ifoSensor['ifoTempName']['value'], // each sensor id must be unique
        'ifotecSensor',
        $ifoSensor['ifoTempDescr']['value'],
        10, // divider
        1, // multiplier
        $ifoSensor['ifoTempLowThldAlarm']['value'] / 10,
        $ifoSensor['ifoTempLowThldWarning']['value'] / 10,
        $ifoSensor['ifoTempHighThldWarning']['value'] / 10,
        $ifoSensor['ifoTempHighThldAlarm']['value'] / 10,
        $ifoSensor['ifoTempValue']['value'] / 10
    );

    $index++;
}
