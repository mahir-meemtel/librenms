<?php
[$devices, $d_more] = include 'devices.inc.php';
[$groups, $g_more] = include 'groups.inc.php';
[$locations, $l_more] = include 'locations.inc.php';

$locations = array_map(function ($location) {
    $location['id'] = 'l' . $location['id'];

    return $location;
}, $locations);

$groups = array_map(function ($group) {
    $group['id'] = 'g' . $group['id'];

    return $group;
}, $groups);

$data = [
    ['text' => 'Locations', 'children' => $locations],
    ['text' => 'Groups', 'children' => $groups],
    ['text' => 'Devices', 'children' => $devices],
];

return [$data, $d_more || $g_more || $l_more];
