<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$unit_text = 'Reply cache';
$unitlen = 15;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'nfs-server-default', $app->app_id]);

$array = [
    'rc_hits' => ['descr' => 'hits', 'colour' => 'B0262D'], // this is bad : retransmitting (red)
    'rc_misses' => ['descr' => 'misses', 'colour' => 'B36326'], // requires caching
    'rc_nocache' => ['descr' => 'nocache', 'colour' => '2B9220'], // no caching needed
];

$i = 0;

foreach ($array as $ds => $var) {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $var['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = $var['colour'];
    $i++;
}

require 'includes/html/graphs/generic_v3_multiline.inc.php';
