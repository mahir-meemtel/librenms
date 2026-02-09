<?php
echo 'Dell UPS: ';
$temp = snmp_get($device, 'physicalOutputPresentConsumption.0', '-Ovqe', 'DELL-SNMP-UPS-MIB');
if (is_numeric($temp) && ! is_null($temp)) {
    $oid = '.1.3.6.1.4.1.674.10902.2.120.2.6.0';
    $descr = 'System Consumption';
    discover_sensor(null, 'power', $device, $oid, '0', 'dell-ups', $descr, '1', '1', null, null, null, null, $temp);
}
