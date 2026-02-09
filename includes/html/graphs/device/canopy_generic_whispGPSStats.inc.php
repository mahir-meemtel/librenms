<?php
$scale_min = 0;

require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-whispGPSStats');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'Value    1 = Synched   2 = Lost Sync    3 = Generating   \\n'";
    $rrd_options .= ' DEF:whispGPSStats=' . $rrdfilename . ':whispGPSStats:AVERAGE ';
    $rrd_options .= ' -l 1 ';
    $rrd_options .= ' -u 3 ';
    $rrd_options .= " LINE2:whispGPSStats#00B8E6:'GPS Status      ' ";
    $rrd_options .= ' GPRINT:whispGPSStats:LAST:%0.2lf%s\\\l ';
}
