<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Jobs';
$unitlen = 15;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', 'ogs', $app->app_id]);

$array = [
    'running_jobs' => ['descr' => 'running'],
    'pending_jobs' => ['descr' => 'pending'],
    'suspend_jobs' => ['descr' => 'suspend'],
    //    'zombie_jobs' => array('descr' => 'zombie') // this is a bad naming, zombies in sge are cached finished jobs
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
