<?php
foreach (snmpwalk_cache_oid($device, 'hpicfSensorTable', [], 'HP-ICF-CHASSIS', null, '-OeQUs') as $index => $data) {
    $state_name = $data['hpicfSensorObjectId'];
    $state_oid = '.1.3.6.1.4.1.11.2.14.11.1.2.6.1.4.';
    $state_descr = $data['hpicfSensorDescr'];
    $state = $data['hpicfSensorStatus'];
    $state_index = $state_name . '.' . $index;

    $states = [
        ['value' => 1, 'generic' => 3, 'graph' => 0, 'descr' => 'unknown'],
        ['value' => 2, 'generic' => 2, 'graph' => 1, 'descr' => 'bad'],
        ['value' => 3, 'generic' => 1, 'graph' => 1, 'descr' => 'warning'],
        ['value' => 4, 'generic' => 0, 'graph' => 1, 'descr' => 'good'],
        ['value' => 5, 'generic' => 3, 'graph' => 0, 'descr' => 'notPresent'],
    ];
    create_state_index($state_name, $states);

    discover_sensor(null, 'state', $device, $state_oid . $index, $state_index, $state_name, $state_descr, '1', '1', null, null, null, null, $state);
}
