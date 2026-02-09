<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'File Handle Stats';
$unitlen = 15;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'nfs-server-default', $app->app_id]);

$array = [
    'fh_lookup' => ['descr' => 'fh_lookup'],
    'fh_anon' => ['descr' => 'fh_anon'],
    'fh_ncachedir' => ['descr' => 'fh_ncachedir'],
    'fh_ncachenondir' => ['descr' => 'fh_ncachenondir'],
    'fh_stale' => ['descr' => 'fh_stale'], // only var that should show something
];

$i = 0;

foreach ($array as $ds => $var) {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $var['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = \App\Facades\ObzoraConfig::get("graph_colours.$colours.$i");
    $i++;
}

require 'includes/html/graphs/generic_v3_multiline.inc.php';
