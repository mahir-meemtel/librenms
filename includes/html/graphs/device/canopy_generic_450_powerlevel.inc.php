<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-450-powerlevel');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'dBm                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:last=' . $rrdfilename . ':last:AVERAGE ';
    $rrd_options .= " LINE2:last#003EFF:'Last      ' ";
    $rrd_options .= ' GPRINT:last:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:last:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:last:MAX:%0.2lf%s\\\l ';
}
