<?php
if (! Auth::user()->hasGlobalAdmin()) {
    $response = [
        'status' => 'error',
        'message' => 'Need to be admin',
    ];
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_POST['device_id'])) {
    if (! is_numeric($_POST['device_id'])) {
        $status = 'error';
        $message = 'Invalid device id ' . $_POST['device_id'];
    } else {
        $result = device_discovery_trigger($_POST['device_id']);
        if (! empty($result['status']) || $result['status'] == '0') {
            $status = 'ok';
        } else {
            $status = 'error';
        }
        $message = $result['message'];
    }
} elseif (isset($_POST['device_group_id'])) {
    if (! is_numeric($_POST['device_group_id'])) {
        $status = 'error';
        $message = 'Invalid device group id ' . $_POST['device_group_id'];
    } else {
        $device_ids = dbFetchColumn('SELECT `device_id` FROM `device_group_device` WHERE `device_group_id` = ?', [$_POST['device_group_id']]);
        $update = 0;
        foreach ($device_ids as $device_id) {
            $result = device_discovery_trigger($device_id);
            $update += $result['status'];
        }

        if (! empty($update) || $update == '0') {
            $status = 'ok';
            $message = 'Devices of group ' . htmlspecialchars($_POST['device_group_id']) . ' will be rediscovered';
        } else {
            $status = 'error';
            $message = 'Error rediscovering devices of group ' . htmlspecialchars($_POST['device_group_id']);
        }
    }
} else {
    $status = 'Error';
    $message = 'Undefined POST keys received';
}

$output = [
    'status' => $status,
    'message' => $message,
];

header('Content-type: application/json');
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
