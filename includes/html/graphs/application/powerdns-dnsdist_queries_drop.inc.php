<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Queries drop';
$unitlen = 16;
$bigdescrlen = 25;
$smalldescrlen = 25;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;
$rrd_filename = Rrd::name($device['hostname'], ['app', $app->app_type, $app->app_id]);

$array = [
    'queries_drop_no_policy' => ['descr' => 'No server', 'colour' => 'aa0635'],
    'queries_drop_nc' => ['descr' => 'Non-compliant', 'colour' => 'cc6985'],
    'queries_drop_nc_answer' => ['descr' => 'Non-compliant answer', 'colour' => '2d2d2d'],
    'queries_acl_drop' => ['descr' => 'ACL', 'colour' => '008442'],
    'queries_failure' => ['descr' => 'Failure', 'colour' => 'e55b38'],
    'queries_serv_fail' => ['descr' => 'Servfail', 'colour' => '9a3e3e'],
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
