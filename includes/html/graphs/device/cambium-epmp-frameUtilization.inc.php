<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'cambium-epmp-frameUtilization');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'%                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:dlwlanframeutilization=' . $rrdfilename . ':dlwlanfrut:AVERAGE ';
    $rrd_options .= ' DEF:ulwlanframeutilization=' . $rrdfilename . ':ulwlanfrut:AVERAGE ';
    $rrd_options .= " LINE2:dlwlanframeutilization#FF0000:'Downlink Frame Utilization       ' ";
    $rrd_options .= ' GPRINT:dlwlanframeutilization:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:dlwlanframeutilization:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:dlwlanframeutilization:MAX:%0.2lf%s\\\l ';
    $rrd_options .= " LINE2:ulwlanframeutilization#003EFF:'Uplink Frame Utilization      ' ";
    $rrd_options .= ' GPRINT:ulwlanframeutilization:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:ulwlanframeutilization:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:ulwlanframeutilization:MAX:%0.2lf%s\\\l ';
}
