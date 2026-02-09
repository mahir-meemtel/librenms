<?php
$temp = snmpwalk_cache_multi_oid($device, 'amperageProbeTableEntry', [], 'MIB-Dell-10892', 'dell');
$cur_oid = '.1.3.6.1.4.1.674.10892.1.600.30.1.6.';

foreach ((array) $temp as $index => $entry) {
    $descr = $entry['amperageProbeLocationName'];
    if ($entry['amperageProbeType'] === 'amperageProbeTypeIsSystemWatts') {
        $divisor = 1;
        (isset($entry['amperageProbeReading'])) ? $value = $entry['amperageProbeReading'] : $value = null;
        (isset($entry['amperageProbeLowerCriticalThreshold'])) ? $lowlimit = $entry['amperageProbeLowerCriticalThreshold'] / $divisor : $lowlimit = null;
        (isset($entry['amperageProbeLowerCriticalThreshold'])) ? $low_warn_limit = $entry['amperageProbeLowerCriticalThreshold'] / $divisor : $low_warn_limit = null;
        (isset($entry['amperageProbeUpperNonCriticalThreshold'])) ? $warnlimit = $entry['amperageProbeUpperNonCriticalThreshold'] / $divisor : $warnlimit = null;
        (isset($entry['amperageProbeUpperCriticalThreshold'])) ? $limit = $entry['amperageProbeUpperCriticalThreshold'] / $divisor : $limit = null;

        discover_sensor(null, 'power', $device, $cur_oid . $index, $index, 'dell', $descr, $divisor, '1', $lowlimit, $low_warn_limit, $warnlimit, $limit, $value, 'snmp', $index);
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
