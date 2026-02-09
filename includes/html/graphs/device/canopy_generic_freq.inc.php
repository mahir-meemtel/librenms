<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-freq');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'Ghz           Now     \\n'";
    $rrd_options .= ' DEF:freq=' . $rrdfilename . ':freq:AVERAGE ';
    $rrd_options .= " LINE2:freq#FF0000:'Frequency       ' ";
    $rrd_options .= ' GPRINT:freq:LAST:%0.2lf%s\\\l ';
}
