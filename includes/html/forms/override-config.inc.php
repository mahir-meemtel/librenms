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

$device['device_id'] = $_POST['device_id'];
$attrib = $_POST['attrib'];
$state = $_POST['state'];
$status = 'error';
$message = 'Error with config';

if (empty($device['device_id'])) {
    $message = 'No device passed';
} else {
    if ($state == true) {
        set_dev_attrib($device, $attrib, $state);
    } else {
        del_dev_attrib($device, $attrib);
    }
    $status = 'ok';
    $message = 'Config has been updated';
}

$response = [
    'status' => $status,
    'message' => $message,
];
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
