<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-450-ptpSNR');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'dBm                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:vertical=' . $rrdfilename . ':vertical:AVERAGE ';
    $rrd_options .= ' DEF:horizontal=' . $rrdfilename . ':horizontal:AVERAGE ';
    $rrd_options .= " LINE2:vertical#FF0000:'Vertical       ' ";
    $rrd_options .= ' GPRINT:vertical:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:vertical:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:vertical:MAX:%0.2lf%s\\\l ';
    $rrd_options .= " LINE2:horizontal#00B2EE:'Horizontal      ' ";
    $rrd_options .= ' GPRINT:horizontal:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:horizontal:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:horizontal:MAX:%0.2lf%s\\\l ';
}
