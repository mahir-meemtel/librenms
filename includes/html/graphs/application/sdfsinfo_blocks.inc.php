<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Blocks';
$unitlen = 6;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'dup_data' => ['descr' => 'Duplicate data wrote (GB)', 'colour' => '000000'],
    'blocks_unique' => ['descr' => 'Unique blocks (GB)', 'colour' => '2A7A12'],
    'blocks_compressed' => ['descr' => 'Compressed blocks (GB)', 'colour' => '74127A'],
    'cluster_copies' => ['descr' => 'Cluster copies', 'colour' => 'F44842'],
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
