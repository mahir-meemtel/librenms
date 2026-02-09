<?php
$charge_oid = '.1.3.6.1.4.1.935.1.1.1.2.2.1.0';
$charge = snmp_get($device, $charge_oid, '-Osqnv');

if (! empty($charge)) {
    $type = 'netagent2';
    $index = 0;
    $limit = null;
    $lowlimit = 0;
    $lowwarnlimit = 10;
    $divisor = 1;
    $descr = 'Battery Charge';

    discover_sensor(
        null,
        'charge',
        $device,
        $charge_oid,
        $index,
        $type,
        $descr,
        $divisor,
        1,
        $lowlimit,
        $lowwarnlimit,
        null,
        $limit,
        $charge
    );
}
