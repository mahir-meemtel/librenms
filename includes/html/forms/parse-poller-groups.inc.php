<?php
if (! Auth::user()->hasGlobalAdmin()) {
    header('Content-type: text/plain');
    exit('ERROR: You need to be admin');
}

$group_id = $_POST['group_id'];

if (is_numeric($group_id) && $group_id > 0) {
    $group = dbFetchRow('SELECT * FROM `poller_groups` WHERE `id` = ? LIMIT 1', [$group_id]);
    $output = [
        'group_name' => $group['group_name'],
        'descr' => $group['descr'],
    ];
    header('Content-type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
