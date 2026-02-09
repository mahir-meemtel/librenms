<?php
require 'includes/html/graphs/common.inc.php';

// escape % characters
$unit = preg_replace('/(?<!%)%(?!%)/', '%%', $unit);

$output_def = 'sensor';
$num = '%5.1lf'; // default: float
if ($unit === '') {
    $num = '%5.0lf';
} elseif ($unit == 'bps') {
    $num .= '%s';
} elseif ($unit == 'Hz') {
    $output_def = 'sensorhz';
    $num = '%5.3lf%s';
}

$sensors = dbFetchRows(
    'SELECT * FROM `wireless_sensors` WHERE `sensor_class` = ? AND `device_id` = ? ORDER BY `sensor_index`',
    [$class, $device['device_id']]
);

if (count($sensors) == 1 && $unit_long == $sensors[0]['sensor_descr']) {
    $unit_long = '';
}

$col_w = 7 + strlen($unit);
$rrd_options .= " COMMENT:'" . str_pad($unit_long, 35) . str_pad('Cur', $col_w) . str_pad('Min', $col_w) . "Max\\n'";

foreach ($sensors as $index => $sensor) {
    $sensor_id = $sensor['sensor_id'];
    $colour_index = $index % count(\App\Facades\ObzoraConfig::get('graph_colours.mixed'));
    $colour = \App\Facades\ObzoraConfig::get("graph_colours.mixed.$colour_index");

    $sensor_descr_fixed = \ObzoraNMS\Data\Store\Rrd::fixedSafeDescr($sensor['sensor_descr'], 28);
    $rrd_file = Rrd::name($device['hostname'], ['wireless-sensor', $sensor['sensor_class'], $sensor['sensor_type'], $sensor['sensor_index']]);
    $rrd_options .= " DEF:sensor$sensor_id=$rrd_file:sensor:AVERAGE";

    if ($unit == 'Hz') {
        $rrd_options .= " CDEF:sensorhz$sensor_id=sensor$sensor_id,1000000,*";
    }

    $rrd_options .= " LINE1.5:$output_def$sensor_id#$colour:'$sensor_descr_fixed'";
    $rrd_options .= " GPRINT:$output_def$sensor_id:LAST:'$num$unit'";
    $rrd_options .= " GPRINT:$output_def$sensor_id:MIN:'$num$unit'";
    $rrd_options .= " GPRINT:$output_def$sensor_id:MAX:'$num$unit'\\l ";
}//end foreach
