<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'cambium-epmp-gpsSync');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'1 - GPS Sync Up       2 - GPS Sync Down      3 - CMM Sync     \\n'";
    $rrd_options .= ' DEF:gpsSync=' . $rrdfilename . ':gpsSync:AVERAGE ';
    $rrd_options .= ' -l 1 ';
    $rrd_options .= ' -u 3 ';
    $rrd_options .= " LINE2:gpsSync#666699:'GPS Sync Status  ' ";
    $rrd_options .= ' GPRINT:gpsSync:LAST:%0.2lf%s ';
}
