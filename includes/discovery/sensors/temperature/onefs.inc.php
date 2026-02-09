<?php
echo 'OneFS: ';
$oids = snmpwalk_cache_multi_oid($device, 'tempSensorTable', [], 'ISILON-MIB');

foreach ($oids as $index => $entry) {
    if (is_numeric($entry['tempSensorValue']) && is_numeric($index)) {
        $descr = $entry['tempSensorDescription'];
        $oid = '.1.3.6.1.4.1.12124.2.54.1.4.' . $index;
        $current = $entry['tempSensorValue'];
        discover_sensor(null, 'temperature', $device, $oid, $index, 'onefs', $descr, '1', '1', null, null, null, null, $current);
    }
}

unset($oids);
