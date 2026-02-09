<?php
if (! Auth::user()->hasGlobalRead()) {
    return [];
}

$query = '';
$params = [];

if (! empty($_REQUEST['search'])) {
    $query .= ' WHERE `transport_name` LIKE ?';
    $params[] = '%' . $_REQUEST['search'] . '%';
}

$total = dbFetchCell("SELECT COUNT(*) FROM `alert_transports` $query", $params);
$more = false;

if (! empty($_REQUEST['limit'])) {
    $limit = (int) $_REQUEST['limit'];
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
    $offset = ($page - 1) * $limit;

    $query .= " LIMIT $offset, $limit";
} else {
    $offset = 0;
}

$sql = "SELECT `transport_id` AS `id`, `transport_name` AS `text`, `transport_type` AS `type` FROM `alert_transports` $query";
$transports = dbFetchRows($sql, $params);

$more = ($offset + count($transports)) < $total;
$transports = array_map(function ($transport) {
    $transport['text'] = ucfirst($transport['type']) . ': ' . $transport['text'];
    unset($transport['type']);

    return $transport;
}, $transports);

$data = [['text' => 'Transports', 'children' => $transports]];

return[$data, $more];
