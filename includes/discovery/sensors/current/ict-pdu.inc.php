<?php
$oids = snmpwalk_cache_oid($device, 'outputEntry', [], 'ICT-DISTRIBUTION-PANEL-MIB');

foreach ($oids as $index => $entry) {
    $output_number = (int) $entry['outputNumber'] + 1;

    $descr = 'Output Current #' . $output_number;
    if ($entry['outputName'] && $entry['outputName'] != '00') {
        $descr .= ' ' . $entry['outputName'];
    }

    $divisor = 1;
    $oid = '.1.3.6.1.4.1.39145.10.8.1.3.' . $index;
    $type = 'ict-pdu';
    $current = (float) $entry['outputCurrent'] / $divisor;

    discover_sensor(null, 'current', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}

// System Current
$systemCurrent = trim(snmp_get($device, 'systemCurrent.0', '-Oqv', 'ICT-DISTRIBUTION-PANEL-MIB'), '" ');
if (! empty($systemCurrent)) {
    $divisor = 1;
    $index = '7.0';
    $descr = 'System Current';
    $type = 'ict-pdu';
    $oid = '.1.3.6.1.4.1.39145.10.7.0';
    $current = $systemCurrent / $divisor;

    discover_sensor(null, 'current', $device, $oid, $index, $type, $descr, $divisor, '1', null, null, null, null, $current);
}
