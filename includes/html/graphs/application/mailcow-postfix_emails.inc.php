<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'E-mail(s)';
$unitlen = 10;
$bigdescrlen = 20;
$smalldescrlen = 20;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'received' => ['descr' => 'Received', 'colour' => '75a832'],
    'delivered' => ['descr' => 'Delivered', 'colour' => '00d644'],
    'forwarded' => ['descr' => 'Forwarded', 'colour' => 'ccff99'],
    'deferred' => ['descr' => 'Deferred', 'colour' => 'ffcc66'],
    'bounced' => ['descr' => 'Bounced', 'colour' => 'cc6600'],
    'rejected' => ['descr' => 'Rejected', 'colour' => 'cc0000'],
    'held' => ['descr' => 'Held', 'colour' => '3366cc'],
    'discarded' => ['descr' => 'Discarded', 'colour' => '1a1a1a'],
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
