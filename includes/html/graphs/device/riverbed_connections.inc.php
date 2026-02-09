<?php
require 'includes/html/graphs/common.inc.php';
$rrd_filename = Rrd::name($device['hostname'], 'riverbed_connections');

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Connections';
$unitlen = 11;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 80;
$data_sources = [
    'half_open' => ['descr' => 'Half open', 'colour' => '66873e'],
    'half_closed' => ['descr' => 'Half closed', 'colour' => 'f49842'],
    'established' => ['descr' => 'Established', 'colour' => '438099'],
    'active' => ['descr' => 'Active', 'colour' => 'af2121'],
    'total' => ['descr' => 'Total', 'colour' => '000000'],
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
