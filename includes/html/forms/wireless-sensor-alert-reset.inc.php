<?php
header('Content-type: text/plain');

// FUA

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

for ($x = 0; $x < count($_POST['sensor_id']); $x++) {
    dbUpdate(
        [
            'sensor_limit' => set_null($_POST['sensor_limit'][$x]),
            'sensor_limit_low' => set_null($_POST['sensor_limit_low'][$x]),
            'sensor_alert' => set_null($_POST['sensor_alert'][$x]),
        ],
        'wireless_sensors',
        '`sensor_id` = ?',
        [$_POST['sensor_id'][$x]]
    );
}
