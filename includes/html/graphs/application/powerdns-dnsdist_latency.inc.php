<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Average latency';
$unitlen = 16;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'latency_100' => ['descr' => 'Last 100 pkts', 'colour' => 'd29ba5'],
    'latency_1000' => ['descr' => 'Last 1000 pkts', 'colour' => 'b75e6e'],
    'latency_10000' => ['descr' => 'Last 10000 pkts', 'colour' => '732634'],
    'latency_1000000' => ['descr' => 'Last 1000000 pkts', 'colour' => '521b25'],
];

$i = 0;

foreach ($array as $ds => $var) {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $var['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = $var['colour'];
    $i++;
}

require 'includes/html/graphs/generic_v3_multiline_float.inc.php';
