<?php
header('Content-type: application/json');

// FUA

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'You need to be admin',
    ]));
}

if (! is_numeric($_POST['device_id']) || ! is_numeric($_POST['sensor_id']) || ! isset($_POST['data'])) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'Invalid values given',
    ]));
} else {
    $update = dbUpdate(
        [$_POST['value_type'] => set_null($_POST['data'], null), 'sensor_custom' => 'Yes'],
        'wireless_sensors',
        '`sensor_id` = ? AND `device_id` = ?',
        [$_POST['sensor_id'], $_POST['device_id']]
    );
    if (! empty($update) || $update == '0') {
        exit(json_encode([
            'status' => 'ok',
            'message' => 'Updated sensor value',
        ]));
    } else {
        exit(json_encode([
            'status' => 'error',
            'message' => 'Failed to update sensor value',
        ]));
    }
}
