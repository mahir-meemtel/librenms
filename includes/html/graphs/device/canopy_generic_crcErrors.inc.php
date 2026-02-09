<?php
require 'includes/html/graphs/common.inc.php';

$rrdfilename = Rrd::name($device['hostname'], 'canopy-generic-crcErrors');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'dBm                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:crcErrors=' . $rrdfilename . ':crcErrors:AVERAGE ';
    $rrd_options .= " LINE2:crcErrors#FF0000:'CRC Errors        ' ";
    $rrd_options .= ' GPRINT:crcErrors:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:crcErrors:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:crcErrors:MAX:%0.2lf%s\\\l ';
}
