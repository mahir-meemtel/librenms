<?php
$entityStatesIndexes = dbFetchRows(
    'SELECT S.entity_state_id, S.entStateLastChanged, P.entPhysicalIndex FROM entityState AS S ' .
    'LEFT JOIN entPhysical AS P USING (entPhysical_id) WHERE S.device_id=?',
    [$device['device_id']]
);

if (! empty($entityStatesIndexes)) {
    echo "\nEntity States: ";

    // index by entPhysicalIndex
    $entityStatesIndexes = array_by_column($entityStatesIndexes, 'entPhysicalIndex');

    $entLC = snmpwalk_group($device, 'entStateLastChanged', 'ENTITY-STATE-MIB', 0);

    foreach (current($entLC) as $index => $changed) {
        if ($changed) { // skip empty entries
            try {
                [$date, $time, $tz] = explode(',', $changed);
                $lastChanged = new DateTime("$date $time", new DateTimeZone($tz));
                $dbLastChanged = new DateTime($entityStatesIndexes[$index]['entStateLastChanged']);
                if ($lastChanged != $dbLastChanged) {
                    // data has changed, fetch it
                    $new_states = snmp_get_multi(
                        $device,
                        [
                            "entStateAdmin.$index",
                            "entStateOper.$index",
                            "entStateUsage.$index",
                            "entStateAlarm.$index",
                            "entStateStandby.$index",
                        ],
                        '-OQUse',
                        'ENTITY-STATE-MIB'
                    );
                    $new_states = $new_states[$index]; // just get values

                    // add entStateLastChanged and update
                    $new_states['entStateLastChanged'] = $lastChanged
                        ->setTimezone(new DateTimeZone(date_default_timezone_get()))
                        ->format('Y-m-d H:i:s');

                    // check if anything has changed
                    $update = array_diff(
                        $new_states,
                        dbFetchRow(
                            'SELECT * FROM entityState WHERE entity_state_id=?',
                            [$entityStatesIndexes[$index]['entity_state_id']]
                        )
                    );

                    if (! empty($update)) {
                        dbUpdate(
                            $update,
                            'entityState',
                            'entity_state_id=?',
                            [$entityStatesIndexes[$index]['entity_state_id']]
                        );
                        d_echo("Updating $index: ", 'U');
                        d_echo($new_states[$index]);
                        continue;
                    }
                }
            } catch (Exception $e) {
                // no update
                d_echo('Error: ' . $e->getMessage() . PHP_EOL);
            }
        }
        echo '.';
    }

    echo PHP_EOL;
}

unset($entityStatesIndexes, $entLC, $lastChanged, $dbLastChanged, $new_states, $update);
