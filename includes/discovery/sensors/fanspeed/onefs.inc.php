<?php
echo 'OneFS: ';
$oids = snmpwalk_cache_multi_oid($device, 'fanTable', [], 'ISILON-MIB');

foreach ($oids as $index => $entry) {
    if (is_numeric($entry['fanSpeed']) && is_numeric($index)) {
        $descr = $entry['fanDescription'];
        $oid = '.1.3.6.1.4.1.12124.2.53.1.4.' . $index;
        $current = $entry['fanSpeed'];
        discover_sensor(null, 'fanspeed', $device, $oid, $index, 'onefs', $descr, '1', '1', 0, 0, 5000, 9000, $current);
    }
}

unset($oids);
