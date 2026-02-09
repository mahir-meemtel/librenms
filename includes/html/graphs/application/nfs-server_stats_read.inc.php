<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'v3 read operations';
$unitlen = 10;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'nfs-server-proc3', $app->app_id]);

$array = [
    'proc3_getattr' => ['descr' => 'Get attributes'],
    'proc3_lookup' => ['descr' => 'Lookup'],
    'proc3_access' => ['descr' => 'Access'],
    'proc3_readlink' => ['descr' => 'Read link'],
    'proc3_readdir' => ['descr' => 'Read dir'],
    'proc3_readdirplus' => ['descr' => 'Read dir plus'],
    'proc3_fsstat' => ['descr' => 'FS stat'],
    'proc3_fsinfo' => ['descr' => 'FS info'],
    'proc3_pathconf' => ['descr' => 'Pathconf'],
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
