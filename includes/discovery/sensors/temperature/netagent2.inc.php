<?php
$ups_temperature_oid = '.1.3.6.1.4.1.935.1.1.1.2.2.3.0';
$ups_temperature = snmp_get($device, $ups_temperature_oid, '-Oqv');

if (! empty($ups_temperature) || $ups_temperature == 0) {
    $type = 'netagent2';
    $index = 0;
    $limit = 110;
    $warnlimit = 50;
    $lowlimit = 0;
    $lowwarnlimit = 6;
    $divisor = 10;
    $temperature = $ups_temperature / $divisor;
    $descr = 'Temperature';

    discover_sensor(
        null,
        'temperature',
        $device,
        $ups_temperature_oid,
        $index,
        $type,
        $descr,
        $divisor,
        '1',
        $lowlimit,
        $lowwarnlimit,
        $warnlimit,
        $limit,
        $temperature
    );
}
