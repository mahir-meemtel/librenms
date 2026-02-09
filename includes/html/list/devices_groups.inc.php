<?php
[$devices, $d_more] = include 'devices.inc.php';
[$groups, $g_more] = include 'groups.inc.php';

$groups = array_map(function ($group) {
    $group['id'] = 'g' . $group['id'];

    return $group;
}, $groups);

$data = [
    ['text' => 'Groups', 'children' => $groups],
    ['text' => 'Devices', 'children' => $devices],
];

return [$data, $d_more || $g_more];
