<?php
$oid = $device['sysObjectID'] . '.1.9.1.1.7';

foreach (SnmpQuery::walk($oid)->values() as $oid => $name) {
    if ($name) {
        $index = \Illuminate\Support\Str::afterLast($oid, '.');
        $port_stats[$index]['ifAlias'] = $name;
    }
}
