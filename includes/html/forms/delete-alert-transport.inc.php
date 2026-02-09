<?php
header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'You need to be admin.',
    ]));
}

$status = 'ok';
$message = '';

if (! is_numeric($vars['transport_id'])) {
    $status = 'error';
    $message = 'No transport selected';
} else {
    if (dbDelete('alert_transports', '`transport_id` = ?', [$vars['transport_id']])) {
        dbDelete('alert_transport_map', '`target_type` = "single" AND `transport_or_group_id` = ?', [$vars['transport_id']]);
        dbDelete('transport_group_transport', '`transport_id`=?', [$vars['transport_id']]);

        $message = 'Alert transport has been deleted';
    } else {
        $message = 'Alert transport has not been deleted';
    }
}

exit(json_encode([
    'status' => $status,
    'message' => $message,
]));
