<?php
$entPhysical = dbFetchRows(
    'SELECT entPhysical_id, entPhysicalIndex FROM entPhysical WHERE device_id=?',
    [$device['device_id']]
);

if (! empty($entPhysical)) {
    echo "\nEntity States: ";

    $entPhysical = array_column($entPhysical, 'entPhysical_id', 'entPhysicalIndex');
    $state_data = snmpwalk_group($device, 'entStateTable', 'ENTITY-STATE-MIB');
    $db_states = dbFetchRows('SELECT * FROM entityState WHERE device_id=?', [$device['device_id']]);
    $db_states = array_by_column($db_states, 'entPhysical_id');

    foreach ($state_data as $index => $state) {
        if (isset($entPhysical[$index])) {
            $id = $entPhysical[$index];

            // format datetime
            if (empty($state['entStateLastChanged'])) {
                $state['entStateLastChanged'] = null;
            } else {
                [$date, $time, $tz] = explode(',', $state['entStateLastChanged']);
                try {
                    $lastChanged = new DateTime("$date $time", new DateTimeZone($tz));
                    $state['entStateLastChanged'] = $lastChanged
                        ->setTimezone(new DateTimeZone(date_default_timezone_get()))
                        ->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    // no update
                }
            }

            if (isset($db_states[$id])) { // update the db
                $db_state = $db_states[$id];
                $update = array_diff($state, $db_state);

                if (! empty($update)) {
                    if (array_key_exists('entStateLastChanged', $update) && is_null($update['entStateLastChanged'])) {
                        $update['entStateLastChanged'] = null;
                    }

                    dbUpdate($update, 'entityState', 'entity_state_id=?', [$db_state['entity_state_id']]);
                    d_echo('Updating entity state: ', 'U');
                    d_echo($update);
                } else {
                    echo '.';
                }

                unset($state_data[$index]); // remove so we don't insert later
                unset($db_states[$id]); // remove so we don't delete later
            } else {
                // prep for insert later
                $state_data[$index]['device_id'] = $device['device_id'];
                $state_data[$index]['entPhysical_id'] = $id;
                $state_data[$index]['entStateLastChanged'] = $state['entStateLastChanged'];
                d_echo('Inserting entity state:: ', '+');
                d_echo($state);
            }
        }
    }

    if (! empty($state_data)) {
        dbBulkInsert($state_data, 'entityState');
    }

    if (! empty($db_states)) {
        dbDelete(
            'entityState',
            'entity_state_id IN ' . dbGenPlaceholders(count($db_states)),
            array_column($db_states, 'entity_state_id')
        );
    }
}

echo PHP_EOL;

unset($entPhysical, $state_data, $db_states, $update);
