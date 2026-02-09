<?php
$battery_current_oid = '.1.3.6.1.4.1.935.1.1.1.2.2.7.0';
$battery_current = snmp_get($device, $battery_current_oid, '-Oqv');

if (! empty($battery_current) || $battery_current == 0) {
    $type = 'netagent2';
    $index = 0;
    $limit = 30;
    $warnlimit = null;
    $lowlimit = null;
    $lowwarnlimit = null;
    $divisor = 10;
    $current = $battery_current / $divisor;
    $descr = 'Battery Current';

    discover_sensor(
        null,
        'current',
        $device,
        $battery_current_oid,
        $index,
        $type,
        $descr,
        $divisor,
        '1',
        $lowlimit,
        $lowwarnlimit,
        $warnlimit,
        $limit,
        $current
    );
}
