<?php
header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'ERROR: You need to be admin.',
    ]));
}

$status = 'ok';
$message = '';

if (! is_numeric($vars['group_id'])) {
    $status = 'error';
    $message = 'ERROR: No transport group selected';
} else {
    if (dbDelete('alert_transport_groups', '`transport_group_id` = ?', [$vars['group_id']])) {
        dbDelete('transport_group_transport', '`transport_group_id`=?', [$vars['group_id']]);
        dbDelete('alert_transport_map', '`target_type`="group" AND `transport_or_group_id`=?', [$vars['group_id']]);
        $message = 'Alert transport group has been deleted';
    } else {
        $message = 'ERROR: Alert transport group has not been deleted';
    }
}

exit(json_encode([
    'status' => $status,
    'message' => $message,
]));
