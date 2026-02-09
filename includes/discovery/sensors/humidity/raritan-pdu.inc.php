<?php
$descr = 'Processor Humidity';
$divisor = 1;
$multiplier = '1';

d_echo('Humidity for Raritan PDU');
$oids = snmpwalk_cache_multi_oid($device, 'externalSensorTable', [], 'PDU-MIB');
$offset = 0;
foreach ($oids as $index => $sensor) {
    if ($sensor['externalSensorType'] == 'humidity') {
        $oid = ".1.3.6.1.4.1.13742.4.3.3.1.41.$index";
        $descr = $sensor['externalSensorName'];
        $hum_current = $sensor['externalSensorValue'];
        $limit_high = $sensor['externalSensorUpperWarningThreshold'] / $divisor;
        $limit_low = $sensor['externalSensorLowerWarningThreshold'] / $divisor;
        $limit_high_warn = $sensor['externalSensorUpperCriticalThreshold'] / $divisor;
        $limit_low_warn = $sensor['externalSensorLowerCriticalThreshold'] / $divisor;
        $offset++;
        if (is_numeric($hum_current) && $hum_current >= 0) {
            discover_sensor(null, 'humidity', $device, $oid, $offset, 'raritan', $descr, $divisor, $multiplier, $limit_low, $limit_low_warn, $limit_high_warn, $limit_high, $hum_current);
        }
    }
}
