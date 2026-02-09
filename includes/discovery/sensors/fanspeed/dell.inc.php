<?php
$temp = snmpwalk_cache_multi_oid($device, 'coolingDeviceTable', [], 'MIB-Dell-10892', 'dell');
$cur_oid = '.1.3.6.1.4.1.674.10892.1.700.12.1.6.';

if (is_array($temp)) {
    foreach ($temp as $index => $entry) {
        $descr = $temp[$index]['coolingDeviceLocationName'];
        (isset($temp[$index]['coolingDeviceReading'])) ? $value = $temp[$index]['coolingDeviceReading'] : $value = null;
        (isset($temp[$index]['coolingDeviceLowerCriticalThreshold'])) ? $lowlimit = $temp[$index]['coolingDeviceLowerCriticalThreshold'] : $lowlimit = null;
        (isset($temp[$index]['coolingDeviceLowerNonCriticalThreshold'])) ? $low_warn_limit = $temp[$index]['coolingDeviceLowerNonCriticalThreshold'] : $low_warn_limit = null;
        (isset($temp[$index]['coolingDeviceUpperNonCriticalThreshold'])) ? $warnlimit = $temp[$index]['coolingDeviceUpperNonCriticalThreshold'] : $warnlimit = null;
        (isset($temp[$index]['coolingDeviceUpperCriticalThreshold'])) ? $limit = $temp[$index]['coolingDeviceUpperCriticalThreshold'] : $limit = null;

        discover_sensor(null, 'fanspeed', $device, $cur_oid . $index, $index, 'dell', $descr, '0', '1', $lowlimit, $low_warn_limit, $warnlimit, $limit, $value, 'snmp', $index);

        unset(
            $descr,
            $value,
            $lowlimit,
            $low_warn_limit,
            $warnlimit,
            $limit
        );
    }
}
