<?php
header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'You need to be admin',
    ]));
}

$status = 'ok';
$message = '';

$group_id = $vars['group_id'];
$name = strip_tags($vars['name']);

$target_members = [];
foreach ((array) ($vars['members'] ?? []) as $target) {
    $target_members[] = (int) $target;
}

if (empty($name)) {
    $status = 'error';
    $message = 'No transport group name provided';
} elseif (count($target_members) < 1) {
    // Not enough members for a group; requires 1 at least
    $status = 'error';
    $message = 'Not enough group members';
} else {
    if (is_numeric($group_id) && $group_id > 0) {
        dbUpdate([
            'transport_group_name' => $name,
        ], 'alert_transport_groups', '`transport_group_id`=?', [$group_id]);
    } else {
        // Insert into db
        $group_id = dbInsert([
            'transport_group_name' => $name,
        ], 'alert_transport_groups');
    }

    if (is_numeric($group_id) && $group_id > 0) {
        $sql = 'SELECT `transport_id` FROM `transport_group_transport` WHERE `transport_group_id`=?';
        $db_members = dbFetchColumn($sql, [$group_id]);

        // Compare arrays to get added and removed transports
        $add = array_diff($target_members, $db_members);
        $remove = array_diff($db_members, $target_members);

        // Insert new transport group members
        $insert = [];
        foreach ($add as $transport_id) {
            $insert[] = [
                'transport_id' => $transport_id,
                'transport_group_id' => $group_id,
            ];
        }
        if (! empty($insert)) {
            dbBulkInsert($insert, 'transport_group_transport');
        }

        // Remove old transport group members
        if (! empty($remove)) {
            dbDelete('transport_group_transport', 'transport_group_id=? AND `transport_id` IN ' . dbGenPlaceholders(count($remove)), array_merge([$group_id], $remove));
        }
        $message = 'Updated alert transport group';
    } else {
        $status = 'error';
        $message = 'Did not update alert transport group';
    }
}

exit(json_encode([
    'status' => $status,
    'message' => $message,
]));
