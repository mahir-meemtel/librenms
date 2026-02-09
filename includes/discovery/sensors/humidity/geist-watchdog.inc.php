<?php
use ObzoraNMS\Util\Number;

$value = Number::cast(SnmpQuery::get('GEIST-MIB-V3::climateHumidity')->value());
if ($value) {
    $current_oid = '.1.3.6.1.4.1.21239.2.2.1.7.1';
    $descr = 'Humidity';
    discover_sensor(null, 'humidity', $device, $current_oid, 'climateHumidity', 'geist-watchdog', $descr, 1, 1, null, null, null, null, $value);
}
