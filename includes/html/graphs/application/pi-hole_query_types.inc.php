<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Queries';
$unitlen = 6;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'query_a' => ['descr' => 'A Type', 'colour' => '000000'],
    'query_aaaa' => ['descr' => 'AAAA Type', 'colour' => '657C5E'],
    'query_ptr' => ['descr' => 'PTR Type', 'colour' => 'F44842'],
    'query_srv' => ['descr' => 'SRV Type', 'colour' => '912925'],
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
