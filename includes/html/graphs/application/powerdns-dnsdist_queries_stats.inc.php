<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Queries stats';
$unitlen = 16;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'queries_count' => ['descr' => 'Total', 'colour' => '5aa492'],
    'queries_recursive' => ['descr' => 'Recursive', 'colour' => '3e7266'],
    'queries_empty' => ['descr' => 'Empty', 'colour' => 'aa0635'],
    'queries_self_answer' => ['descr' => 'Self answer', 'colour' => '81cdea'],
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
