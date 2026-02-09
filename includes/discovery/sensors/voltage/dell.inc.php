<?php
$temp = snmpwalk_cache_multi_oid($device, 'voltageProbeTable', [], 'MIB-Dell-10892', 'dell');
$cur_oid = '.1.3.6.1.4.1.674.10892.1.600.20.1.6.';

foreach ((array) $temp as $index => $entry) {
    $descr = $entry['voltageProbeLocationName'];
    if ($entry['voltageProbeType'] != 'voltageProbeTypeIsDiscrete') {
        $divisor = 1000;
        (isset($entry['voltageProbeReading'])) ? $value = $entry['voltageProbeReading'] : $value = null;
        (isset($entry['voltageProbeLowerCriticalThreshold'])) ? $lowlimit = $entry['voltageProbeLowerCriticalThreshold'] / $divisor : $lowlimit = null;
        (isset($entry['voltageProbeLowerCriticalThreshold'])) ? $low_warn_limit = $entry['voltageProbeLowerCriticalThreshold'] / $divisor : $low_warn_limit = null;
        (isset($entry['voltageProbeUpperNonCriticalThreshold'])) ? $warnlimit = $entry['voltageProbeUpperNonCriticalThreshold'] / $divisor : $warnlimit = null;
        (isset($entry['voltageProbeUpperCriticalThreshold'])) ? $limit = $entry['voltageProbeUpperCriticalThreshold'] / $divisor : $limit = null;

        discover_sensor(null, 'voltage', $device, $cur_oid . $index, $index, 'dell', $descr, $divisor, '1', $lowlimit, $low_warn_limit, $warnlimit, $limit, $value, 'snmp', $index);
    }

    unset(
        $descr,
        $value,
        $lowlimit,
        $low_warn_limit,
        $warnlimit,
        $limit
    );
}

unset(
    $temp,
    $cur_oid,
    $index,
    $entry
);
