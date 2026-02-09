<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Volume';
$unitlen = 6;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);
$array = [
    'files' => ['descr' => 'Files', 'colour' => '000000'],
    'vol_capacity' => ['descr' => 'Capacity (GB)', 'colour' => '2A7A12'],
    'vol_logic_size' => ['descr' => 'Logic capacity (GB)', 'colour' => '74127A'],
    'vol_max_load' => ['descr' => 'Max load (%)', 'colour' => 'F44842'],
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
