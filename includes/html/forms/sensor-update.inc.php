<?php
header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    $response = [
        'status' => 'error',
        'message' => 'Need to be admin',
    ];
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$status = 'error';
$message = 'Error updating sensor limit';
$device_id = $_POST['device_id'];
$sensor_id = $_POST['sensor_id'];
$value_type = $_POST['value_type'];
$data = $_POST['data'];

if (! is_numeric($device_id)) {
    $message = 'Missing device id';
} elseif (! is_numeric($sensor_id)) {
    $message = 'Missing sensor id';
} elseif (! isset($data)) {
    $message = 'Missing data';
} else {
    if (dbUpdate([$value_type => set_null($data), 'sensor_custom' => 'Yes'], 'sensors', '`sensor_id` = ? AND `device_id` = ?', [$sensor_id, $device_id]) >= 0) {
        $message = 'Sensor value updated';
        $status = 'ok';
    } else {
        $message = 'Could not update sensor value';
    }
}

$response = [
    'status' => $status,
    'message' => $message,
];
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
