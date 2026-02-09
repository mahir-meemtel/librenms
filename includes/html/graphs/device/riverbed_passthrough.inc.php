<?php
require 'includes/html/graphs/common.inc.php';
$rrd_filename = Rrd::name($device['hostname'], 'riverbed_passthrough');

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Bandwidth Passthrough';
$unitlen = 21;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 80;
$data_sources = [
    'bw_in' => ['descr' => 'In', 'colour' => '30649b'],
    'bw_out' => ['descr' => 'Out', 'colour' => '9b3030'],
    'bw_total' => ['descr' => 'Total', 'colour' => '000000'],
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

require 'includes/html/graphs/generic_v3_multiline_float.inc.php';
