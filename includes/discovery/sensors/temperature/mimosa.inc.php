<?php
d_echo('Mimosa');
$oid = '.1.3.6.1.4.1.43356.2.1.2.1.8.0';
$index = 0;
$sensor_type = 'mimosaInternalTemp';
$descr = 'Internal Temp';
$divisor = 10;
$temperature = (snmp_get($device, $oid, '-Oqv') / $divisor);
if (is_numeric($temperature)) {
    discover_sensor(null, 'temperature', $device, $oid, $index, $sensor_type, $descr, $divisor, '1', '0', null, null, '65', $temperature);
}
