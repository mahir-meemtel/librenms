<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Operations';
$unitlen = 10;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'nfs-server-default', $app->app_id]);

$array = [
    'io_read' => ['descr' => 'read', 'colour' => '2B9220'],
    'io_write' => ['descr' => 'write', 'colour' => 'B0262D'],
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

/*
This would create a graph with reads above and write belows;
I can't find out how to adapt the legend. If you wish to swap graphs,
uncomment all the above untill <?php and uncomment below this note

$rrd_filename  = rrd_name($device['hostname'], array('app', 'nfs-server-default', $app->app_id));

$ds_in  = 'io_read';
$ds_out = 'io_write';

require 'includes/html/graphs/generic_data.inc.php';

*/
