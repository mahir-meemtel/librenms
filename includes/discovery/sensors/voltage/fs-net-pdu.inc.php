<?php
$oid = '.1.3.6.1.4.1.30966.10.3.2.1.0';
$voltage = snmp_get($device, $oid, '-Oqv');
if ($voltage > 0) {
    discover_sensor(null, 'voltage', $device, $oid, 0, 'PDU L1', 'Voltage', 1, 1, null, null, null, null, $voltage);
}
