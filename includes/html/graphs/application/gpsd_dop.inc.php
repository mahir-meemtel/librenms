<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'DOP';
$nototal = 1;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'gpsd', $app->app_id]);
$array = [
    'hdop' => ['descr' => 'Horizontal'],
    'vdop' => ['descr' => 'Vertical'],
];

$i = 0;

if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($array as $ds => $var) {
        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = $var['descr'];
        $rrd_list[$i]['ds'] = $ds;
        $rrd_list[$i]['colour'] = \App\Facades\ObzoraConfig::get("graph_colours.$colours" . ($i + 2));
        $i++;
    }
} else {
    throw new \ObzoraNMS\Exceptions\RrdGraphException("No Data file $rrd_filename");
}

require 'includes/html/graphs/generic_multi_line.inc.php';
