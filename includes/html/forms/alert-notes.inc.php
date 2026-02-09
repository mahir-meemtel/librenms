<?php
header('Content-type: application/json');

$alert_id = $vars['alert_id'];
$sub_type = $vars['sub_type'];
$note = strip_tags($vars['note']) ?: '';
$status = 'error';

if (is_numeric($alert_id)) {
    if ($sub_type === 'get_note') {
        $note = dbFetchCell('SELECT `note` FROM `alerts` WHERE `id` = ?', [$alert_id]);
        $message = 'Alert note retrieved';
        $status = 'ok';
    } else {
        if (dbUpdate(['note' => $note], 'alerts', '`id` = ?', [$alert_id])) {
            $status = 'ok';
            $message = 'Note updated';
        } else {
            $message = 'Could not update note';
        }
    }
} else {
    $message = 'Invalid alert id';
}
exit(json_encode([
    'status' => $status,
    'message' => $message,
    'note' => $note,
]));
