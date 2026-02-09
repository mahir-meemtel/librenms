<?php
header('Content-type: application/json');

$device_hostname = strip_tags($_POST['device_hostname']);
if (Auth::user()->hasGlobalAdmin() && isset($device_hostname)) {
    if ((new \App\ApiClients\Oxidized())->updateNode($device_hostname, 'ObzoraNMS GUI refresh', Auth::user()->username)) {
        $status = 'ok';
        $message = 'Queued refresh in oxidized for device ' . $device_hostname;
    } else {
        $status = 'error';
        $message = 'ERROR: Could not queue refresh of oxidized device ' . $device_hostname;
    }
} else {
    $status = 'error';
    $message = 'ERROR: Could not queue refresh oxidized device';
}

$output = [
    'status' => $status,
    'message' => $message,
];

header('Content-type: application/json');
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
