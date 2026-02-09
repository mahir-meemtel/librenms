<?php
if (! Auth::user()->hasGlobalRead()) {
    return [];
}

$query = '';
$params = [];

if (! empty($_REQUEST['search'])) {
    $query .= ' WHERE `location` LIKE ?';
    $params[] = '%' . $_REQUEST['search'] . '%';
}

$total = dbFetchCell("SELECT COUNT(*) FROM `locations` $query", $params);
$more = false;

if (! empty($_REQUEST['limit'])) {
    $limit = (int) $_REQUEST['limit'];
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
    $offset = ($page - 1) * $limit;

    $query .= " LIMIT $offset, $limit";
} else {
    $offset = 0;
}

$sql = "SELECT `id`, `location` AS `text` FROM `locations` $query order by `location`";
$locations = dbFetchRows($sql, $params);

$more = ($offset + count($locations)) < $total;

return [$locations, $more];
