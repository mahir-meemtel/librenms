<?php
$divisor = '1';
$type = 'pcoweb';
$compressors = [
    '.1.3.6.1.4.1.9839.2.1.1.1.0', //compressore1.0
    '.1.3.6.1.4.1.9839.2.1.1.2.0', //compressore2.0
    '.1.3.6.1.4.1.9839.2.1.1.3.0', //compressore3.0
    '.1.3.6.1.4.1.9839.2.1.1.4.0',  //compressore4.0
];

foreach ($compressors as $compressor_oid) {
    $current = snmp_get($device, $compressor_oid, '-OqvU', 'CAREL-ug40cdz-MIB');
    $split_oid = explode('.', $compressor_oid);
    $number = $split_oid[count($split_oid) - 2];
    $index = 'comp_' . $number;
    $descr = 'Compressor ' . $number;
    discover_sensor(null, 'state', $device, $compressor_oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}
