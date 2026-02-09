<?php
if (! Auth::user()->hasGlobalRead()) {
    return [];
}

[$transports, $t_more] = include 'transports.inc.php';

$query = '';
$params = [];

if (! empty($_REQUEST['search'])) {
    $query .= ' WHERE `transport_group_name` LIKE ?';
    $params[] = '%' . $_REQUEST['search'] . '%';
}

$total = dbFetchCell("SELECT COUNT(*) FROM `alert_transport_groups` $query", $params);
$more = false;

if (! empty($_REQUEST['limit'])) {
    $limit = (int) $_REQUEST['limit'];
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
    $offset = ($page - 1) * $limit;

    $query .= " LIMIT $offset, $limit";
} else {
    $offset = 0;
}

$sql = "SELECT `transport_group_id` AS `id`, `transport_group_name` AS `text` FROM `alert_transport_groups` $query";
$groups = dbFetchRows($sql, $params);
$more = ($offset + count($groups)) < $total;
$groups = array_map(function ($group) {
    $group['text'] = 'Group: ' . $group['text'];
    $group['id'] = 'g' . $group['id'];

    return $group;
}, $groups);

$data = [['text' => 'Transport Groups', 'children' => $groups], $transports[0]];

return[$data, $more || $t_more];
