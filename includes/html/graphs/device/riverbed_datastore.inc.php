<?php
require 'includes/html/graphs/common.inc.php';
$rrd_filename = Rrd::name($device['hostname'], 'riverbed_datastore');

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Datastore';
$unitlen = 9;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 80;
$data_sources = [
    'datastore_hits' => ['descr' => 'Hits', 'colour' => '438099'],
    'datastore_miss' => ['descr' => 'Miss', 'colour' => 'af2121'],
];

$i = 0;

if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($data_sources as $ds => $var) {
        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = $var['descr'];
        $rrd_list[$i]['ds'] = $ds;
        $rrd_list[$i]['colour'] = $var['colour'];
        $i++;
    }
} else {
    echo "file missing: $rrd_filename";
}

require 'includes/html/graphs/generic_v3_multiline.inc.php';
