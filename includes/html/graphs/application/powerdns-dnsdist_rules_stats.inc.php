<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Rule stats';
$unitlen = 12;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'rule_drop' => ['descr' => 'Drop', 'colour' => '7a0d18'],
    'rule_nxdomain' => ['descr' => 'NXDomain', 'colour' => 'e0b7bb'],
    'rule_refused' => ['descr' => 'Refused', 'colour' => 'ad404b'],
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
