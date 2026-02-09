<?php
$state_name = 'cardMode';
$states = [
    ['value' => 0, 'generic' => 3, 'graph' => 0, 'descr' => 'notapplicable'],
    ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'normal'],
    ['value' => 2, 'generic' => 1, 'graph' => 0, 'descr' => 'regen'],
];
create_state_index($state_name, $states);

$num_oid = '.1.3.6.1.4.1.42229.1.2.3.3.1.1.10.';

foreach ($pre_cache['infineragroove_slotTable'] as $index => $data) {
    if (is_array($data) && isset($data['cardMode'])) {
        // discover sensors
        $descr = 'slot-' . str_replace('.', '/', $index) . ' (' . $data['slotActualCardType'] . ')';
        discover_sensor(null, 'state', $device, $num_oid . $index, $index, $state_name, $descr, '1', '1', null, null, null, null, $data['cardMode'], 'snmp', $index);
    }
}
