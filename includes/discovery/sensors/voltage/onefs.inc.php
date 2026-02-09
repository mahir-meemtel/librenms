<?php
echo 'OneFS: ';
$oids = snmpwalk_cache_multi_oid($device, 'powerSensorTable', [], 'ISILON-MIB');

foreach ($oids as $index => $entry) {
    if (is_numeric($entry['powerSensorValue']) && is_numeric($index)) {
        $descr = $entry['powerSensorDescription'];
        $oid = '.1.3.6.1.4.1.12124.2.55.1.4.' . $index;
        $current = $entry['powerSensorValue'];
        discover_sensor(null, 'voltage', $device, $oid, $index, 'onefs', $descr, '1', '1', null, null, null, null, $current);
    }
}

unset($oids);
