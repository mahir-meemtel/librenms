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
$message = 'Error updating routing information';

$device_id = $_POST['device_id'];
$routing_id = $_POST['routing_id'];
$data = $_POST['data'];

if (! is_numeric($device_id)) {
    $message = 'Missing device id';
} elseif (! is_numeric($routing_id)) {
    $message = 'Missing routing id';
} else {
    if (dbUpdate(['bgpPeerDescr' => $data], 'bgpPeers', '`bgpPeer_id`=? AND `device_id`=?', [$routing_id, $device_id]) >= 0) {
        $message = 'Routing information updated';
        $status = 'ok';
    } else {
        $message = 'Could not update Routing information';
    }
}

$response = [
    'status' => $status,
    'message' => $message,
    'extra' => $extra,
];
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
