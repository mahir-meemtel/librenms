<?php
$oid = '.1.3.6.1.4.1.32050.2.1.27.5.4';
$current = (snmp_get($device, $oid, '-Oqv') / 10);
if ($current > 0) {
    discover_sensor(null, 'current', $device, $oid, 0, 'sitemonitor', 'Current', 10, 1, null, null, null, null, $current);
}
