<?php
echo 'PBN ';

$multiplier = 1;
$divisor = 256;
foreach ($pre_cache['pbn_oids'] as $index => $entry) {
    if (is_numeric($entry['temperature']) && ($entry['temperature'] !== '-65535')) {
        $oid = '.1.3.6.1.4.1.11606.10.9.63.1.7.1.4.' . $index;
        $port = PortCache::getByIfIndex($index, $device['device_id']);
        $descr = $port?->ifDescr . ' Temperature';
        $limit_low = -256;
        $warn_limit_low = 10;
        $limit = 256;
        $warn_limit = 80;
        $value = $entry['temperature'] / $divisor;
        $entPhysicalIndex = $index;
        $entPhysicalIndex_measured = 'ports';
        discover_sensor(null, 'temperature', $device, $oid, '' . $index, 'pbn', $descr, $divisor, $multiplier, $limit_low, $warn_limit_low, $warn_limit, $limit, $value, 'snmp', $entPhysicalIndex, $entPhysicalIndex_measured);
    }
}
