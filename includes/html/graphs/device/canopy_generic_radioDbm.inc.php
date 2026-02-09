<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-radioDbm');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'dBm                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:dbm=' . $rrdfilename . ':dbm:AVERAGE ';
    $rrd_options .= ' DEF:min=' . $rrdfilename . ':min:AVERAGE ';
    $rrd_options .= ' DEF:max=' . $rrdfilename . ':max:AVERAGE ';
    $rrd_options .= ' DEF:avg=' . $rrdfilename . ':avg:AVERAGE ';
    $rrd_options .= " LINE2:dbm#00E5EE:'Radio Dbm       ' ";
    $rrd_options .= ' GPRINT:dbm:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:dbm:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:dbm:MAX:%0.2lf%s\\\l ';
    $rrd_options .= " LINE2:min#00CD66:'Min       ' ";
    $rrd_options .= ' GPRINT:min:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:min:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:min:MAX:%0.2lf%s\\\l ';
    $rrd_options .= " LINE2:max#B272A6:'Max       ' ";
    $rrd_options .= ' GPRINT:max:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:max:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:max:MAX:%0.2lf%s\\\l ';
    $rrd_options .= " LINE2:avg#CC7F32:'Avg       ' ";
    $rrd_options .= ' GPRINT:avg:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:avg:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:avg:MAX:%0.2lf%s\\\l ';
}
