<?php
$tables = [
    ['num_oid' => '.1.3.6.1.4.1.9.9.661.1.3.2.1.6.',    'oid' => 'c3gGsmNumberOfNearbyCell',   'state_name' => 'c3gGsmNumberOfNearbyCell',   'mib' => 'CISCO-WAN-3G-MIB',       'descr' => 'Nearby cells'],
    ['num_oid' => '.1.3.6.1.4.1.9.9.817.1.1.1.1.1.6.',    'oid' => 'cwceLteCurrOperatingBand', 'state_name' => 'cwceLteCurrOperatingBand',   'mib' => 'CISCO-WAN-CELL-EXT-MIB', 'descr' => 'Cellular operating band'],
];

foreach ($tables as $tablevalue) {
    $temp = snmpwalk_cache_multi_oid($device, $tablevalue['oid'], [], $tablevalue['mib']);
    $cur_oid = $tablevalue['num_oid'];
    $state_name = $tablevalue['state_name'];
    foreach ($temp as $index => $entry) {
        //Discover Sensors
        if ($state_name == 'c3gGsmNumberOfNearbyCell' || $state_name == 'cwceLteCurrOperatingBand') {
            $descr = snmp_get($device, 'entPhysicalName.' . $index, '-Oqv', 'ENTITY-MIB') . ' - ' . $tablevalue['descr'];
        } else {
            $descr = ucwords($temp[$index][$tablevalue['descr']]);
        }
        discover_sensor(null, 'count', $device, $cur_oid . $index, $index, $state_name, $descr, 1, 1, null, null, null, null, $temp[$index][$tablevalue['state_name']], 'snmp', $index);
    }
}
