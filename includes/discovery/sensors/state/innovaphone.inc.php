<?php
$oids = snmpwalk_cache_oid($device, 'voiceIfTable', [], 'INNO-MIB');

if (! empty($oids)) {
    //Create State Index
    $state_name = 'voiceIfState';
    $states = [
        ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'down'],
        ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'up'],
    ];
    create_state_index($state_name, $states);
    $num_oid = '.1.3.6.1.4.1.6666.2.1.1.1.4.';
    foreach ($oids as $index => $entry) {
        $ifname = hex2bin(str_replace(' ', '', $entry['voiceIfName']));
        if (empty($ifname)) {
            $ifname = $entry['voiceIfName'];
        }
        $name = 'Interface ' . $ifname;
        //Discover Sensors
        discover_sensor(null, 'state', $device, $num_oid . $index, $index, $state_name, $name, '1', '1', null, null, null, null, $entry['voiceIfState'], 'snmp', $index);
    }
}
