<?php
$tables = [
    ['cpqDaPhyDrvStatus', '.1.3.6.1.4.1.232.3.2.5.1.1.6.', 'Status', 'CPQIDA-MIB', [
        ['value' => 0, 'generic' => 3, 'graph' => 0, 'descr' => 'noDisk'],
        ['value' => 1, 'generic' => 3, 'graph' => 0, 'descr' => 'other'],
        ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'ok'],
        ['value' => 3, 'generic' => 2, 'graph' => 0, 'descr' => 'failed'],
        ['value' => 4, 'generic' => 2, 'graph' => 0, 'descr' => 'predictiveFailure'],
        ['value' => 5, 'generic' => 1, 'graph' => 0, 'descr' => 'erasing'],
        ['value' => 6, 'generic' => 1, 'graph' => 0, 'descr' => 'eraseDone'],
        ['value' => 7, 'generic' => 1, 'graph' => 0, 'descr' => 'eraseQueued'],
        ['value' => 8, 'generic' => 2, 'graph' => 0, 'descr' => 'ssdWearOut'],
        ['value' => 9, 'generic' => 3, 'graph' => 0, 'descr' => 'notAuthenticated'],
    ]],
    ['cpqDaPhyDrvSmartStatus', '.1.3.6.1.4.1.232.3.2.5.1.1.57.', 'S.M.A.R.T.', 'CPQIDA-MIB', [
        ['value' => 1, 'generic' => 3, 'graph' => 0, 'descr' => 'other'],
        ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'ok'],
        ['value' => 3, 'generic' => 1, 'graph' => 0, 'descr' => 'replaceDrive'],
        ['value' => 4, 'generic' => 1, 'graph' => 0, 'descr' => 'replaceDriveSSDWearOut'],
    ]],
];

foreach ($tables as $tablevalue) {
    [$oid, $num_oid, $descr, $mib, $states] = $tablevalue;
    $temp = snmpwalk_cache_multi_oid($device, $oid, [], $mib, 'hp', '-OQUse');

    if (! empty($temp)) {
        //Create State Index
        $state_name = $oid;
        $state_index_id = create_state_index($state_name, $states);

        foreach ($temp as $index => $entry) {
            $drive_bay = snmp_get($device, "cpqDaPhyDrvBay.$index", '-Ovqn', 'CPQIDA-MIB', 'hp');

            //Discover Sensors
            discover_sensor(
                null,
                'state',
                $device,
                $num_oid . $index,
                $index,
                $state_name,
                "Drive  $drive_bay $descr",
                1,
                1,
                null,
                null,
                null,
                null,
                $entry[$oid],
                'snmp',
                $index
            );
        }
    }
}
