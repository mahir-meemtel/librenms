<?php
$states = [
    'power' => [
        ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Power On'],
        ['value' => 0, 'generic' => 1, 'graph' => 0, 'descr' => 'Power Off'],
    ],
    'sync' => [
        ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Sync Enabled'],
        ['value' => 0, 'generic' => 1, 'graph' => 0, 'descr' => 'Sync Off'],
    ],
];
$octetSetup = [
    [
        'oid' => 'portOnM.0',
        'state_name' => 'portOnM',
        'states' => $states['power'],
        'name' => 'Master Port Enabled',
        'num_oid' => '.1.3.6.1.4.1.25868.1.12.0',
    ],
    [
        'oid' => 'portSyncM.0',
        'state_name' => 'portSyncM',
        'states' => $states['sync'],
        'name' => 'Master Port Sync Status',
        'num_oid' => '.1.3.6.1.4.1.25868.1.13.0',
    ],
    [
        'oid' => 'portOnS.0',
        'state_name' => 'portOnS',
        'states' => $states['power'],
        'name' => 'Slave Port Enabled',
        'num_oid' => '.1.3.6.1.4.1.25868.1.29.0',
    ],
    [
        'oid' => 'portSyncS.0',
        'state_name' => 'portSyncS',
        'states' => $states['sync'],
        'name' => 'Slave Port Sync Status',
        'num_oid' => '.1.3.6.1.4.1.25868.1.30.0',
    ],
];

foreach ($octetSetup as $entry) {
    $octetString = snmp_get($device, $entry['oid'], '-Ovqe', 'CTMMIBCUSTOM');
    if ($octetString) {
        $onStates = explode(',', $octetString);

        create_state_index($entry['state_name'], $entry['states']);

        foreach ($onStates as $index => $value) {
            $port_number = $index + 1;
            discover_sensor(
                null,
                'state',
                $device,
                $entry['num_oid'],
                $port_number,
                $entry['state_name'],
                $entry['name'] . " $port_number",
                1,
                1,
                null,
                null,
                null,
                null,
                $value,
                'snmp',
                $port_number
            );
        }
    }
    unset($octetString, $states, $octetSetup, $port_number);
}
