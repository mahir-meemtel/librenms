<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-jitter');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:jitter=' . $rrdfilename . ':jitter:AVERAGE ';
    $rrd_options .= " AREA:jitter#3333cc:'Jitter       ' ";
    $rrd_options .= ' GPRINT:jitter:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:jitter:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:jitter:MAX:%0.2lf%s\\\l ';
}
