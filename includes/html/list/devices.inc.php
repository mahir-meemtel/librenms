<?php
$query = '';
$where = [];
$params = [];

if (! Auth::user()->hasGlobalRead()) {
    $device_ids = Permissions::devicesForUser()->toArray() ?: [0];
    $where[] = ' `devices`.`device_id` IN ' . dbGenPlaceholders(count($device_ids));
    $params = array_merge($params, $device_ids);
}

if (! empty($_REQUEST['search'])) {
    $where[] = '(`hostname` LIKE ? OR `sysName` LIKE ? OR `display` LIKE ?)';
    $search = '%' . $_REQUEST['search'] . '%';
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

if (! empty($where)) {
    $query .= ' WHERE ';
    $query .= implode(' AND ', $where);
}

$total = dbFetchCell("SELECT COUNT(*) FROM `devices` $query", $params);
$more = false;

if (! empty($_REQUEST['limit'])) {
    $limit = (int) $_REQUEST['limit'];
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
    $offset = ($page - 1) * $limit;

    $query .= " LIMIT $offset, $limit";
} else {
    $offset = 0;
}

$sql = "SELECT `device_id`, `hostname`, `sysName`, `display` FROM `devices` $query";
$devices = array_map(function ($device) {
    return [
        'id' => $device['device_id'],
        'text' => format_hostname($device),
    ];
}, dbFetchRows($sql, $params));

$more = ($offset + count($devices)) < $total;

array_multisort(array_column($devices, 'text'), SORT_ASC, $devices);

return [$devices, $more];
