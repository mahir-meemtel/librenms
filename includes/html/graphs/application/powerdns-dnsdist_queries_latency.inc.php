<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Queries answer latency';
$unitlen = 6;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'latency_0_1' => ['descr' => '< 1ms', 'colour' => '58b146'],
    'latency_1_10' => ['descr' => '1-10 ms', 'colour' => '4f9f3f'],
    'latency_10_50' => ['descr' => '10-50 ms', 'colour' => '3d7b31'],
    'latency_50_100' => ['descr' => '50-100 ms', 'colour' => '23461c'],
    'latency_100_1000' => ['descr' => '100-1000 ms', 'colour' => '11230e'],
    'latency_slow' => ['descr' => '> 1 sec', 'colour' => '727F8C'],
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
