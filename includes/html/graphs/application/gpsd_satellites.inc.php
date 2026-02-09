<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Satellites';
$nototal = 1;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'gpsd', $app->app_id]);
$array = [
    'satellites' => ['descr' => 'Visible', 'area' => true],
    'satellites_used' => ['descr' => 'Used', 'area' => true],
];

$i = 0;

if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($array as $ds => $var) {
        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = $var['descr'];
        $rrd_list[$i]['ds'] = $ds;
        $rrd_list[$i]['colour'] = \App\Facades\ObzoraConfig::get("graph_colours.$colours.$i");
        $rrd_list[$i]['area'] = $var['area'];
        $i++;
    }
} else {
    throw new \ObzoraNMS\Exceptions\RrdGraphException("No Data file $rrd_filename");
}

require 'includes/html/graphs/generic_multi_line.inc.php';
