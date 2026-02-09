<?php
if (! Auth::user()->hasGlobalAdmin()) {
    $status = 'error';
    $message = 'ERROR: You need to be admin to reload Oxidized node list';
} else {
    (new \App\ApiClients\Oxidized())->reloadNodes();
    $status = 'ok';
    $message = 'Oxidized node list was reloaded';
}
$output = [
    'status' => $status,
    'message' => $message,
];
header('Content-type: application/json');
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
