<?php
use App\Facades\ObzoraConfig;

$query = 'SELECT `sensor_class` FROM `sensors` WHERE `device_id` = ?';
$params = [$device['device_id']];

$submodules = ObzoraConfig::get('poller_submodules.sensors', []);
if (! empty($submodules)) {
    $query .= ' AND `sensor_class` IN ' . dbGenPlaceholders(count($submodules));
    $params = array_merge($params, $submodules);
}

$query .= ' GROUP BY `sensor_class`';

foreach (dbFetchRows($query, $params) as $sensor_type) {
    poll_sensor($device, $sensor_type['sensor_class']);
}

unset($submodules, $sensor_type, $query, $params);
