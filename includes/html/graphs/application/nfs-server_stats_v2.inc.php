<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$colours = 'mixed';
$unit_text = 'NFS v2 Operations';
$unitlen = 10;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 33;

$rrd_filename = Rrd::name($device['hostname'], ['app', 'nfs-server-proc2', $app->app_id]);

$array = [
    'proc2_null' => ['descr' => 'Null'],
    'proc2_getattr' => ['descr' => 'Get attributes'],
    'proc2_setattr' => ['descr' => 'Set attributes'],
    'proc2_root' => ['descr' => 'Root'],
    'proc2_lookup' => ['descr' => 'Lookup'],
    'proc2_readlink' => ['descr' => 'ReadLink'],
    'proc2_read' => ['descr' => 'Read'],
    'proc2_wrcache' => ['descr' => 'Wrcache'],
    'proc2_write' => ['descr' => 'Write'],
    'proc2_create' => ['descr' => 'Create'],
    'proc2_remove' => ['descr' => 'Remove'],
    'proc2_rename' => ['descr' => 'Rename'],
    'proc2_link' => ['descr' => 'Link'],
    'proc2_symlink' => ['descr' => 'Symlink'],
    'proc2_mkdir' => ['descr' => 'Mkdir'],
    'proc2_rmdir' => ['descr' => 'Rmdir'],
    'proc2_readdir' => ['descr' => 'Readdir'],
    'proc2_fsstat' => ['descr' => 'fsstat'],
];

$i = 0;

foreach ($array as $ds => $var) {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $var['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = \App\Facades\ObzoraConfig::get("graph_colours.default.$i");
    $i++;
}

require 'includes/html/graphs/generic_v3_multiline.inc.php';
