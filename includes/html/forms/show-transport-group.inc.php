<?php
header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'You need to be admin',
    ]));
}

$group_id = $vars['group_id'];
// Retrieve alert transport
if (is_numeric($group_id) && $group_id > 0) {
    $name = dbFetchCell('SELECT `transport_group_name` FROM `alert_transport_groups` WHERE `transport_group_id`=? LIMIT 1', [$group_id]);

    $query = 'SELECT `a`.`transport_id`, `transport_type`, `transport_name` FROM `transport_group_transport` AS `a` LEFT JOIN `alert_transports` AS `b` ON `a`.`transport_id`=`b`.`transport_id` WHERE `transport_group_id`=?';

    $members = [];
    foreach (dbFetchRows($query, [$group_id]) as $member) {
        $members[] = [
            'id' => $member['transport_id'],
            'text' => ucfirst($member['transport_type']) . ': ' . $member['transport_name'],
        ];
    }
}

if (is_array($members)) {
    exit(json_encode([
        'name' => $name,
        'members' => $members,
    ]));
} else {
    exit(json_encode([
        'status' => 'error',
        'message' => 'No transport group found',
    ]));
}
