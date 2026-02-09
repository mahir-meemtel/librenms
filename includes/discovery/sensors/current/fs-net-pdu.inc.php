<?php
$oid = '.1.3.6.1.4.1.30966.10.3.2.4.0';
$current = snmp_get($device, $oid, '-Oqv') / 10;
if ($current > 0) {
    discover_sensor(null, 'current', $device, $oid, 0, 'PDU L1', 'Current', 10, 1, null, null, null, null, $current);
}
