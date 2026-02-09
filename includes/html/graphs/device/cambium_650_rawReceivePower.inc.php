<?php
require 'includes/html/graphs/common.inc.php';

$rrdfilename = Rrd::name($device['hostname'], 'cambium-650-rawReceivePower');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'dBm                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:rawReceivePower=' . $rrdfilename . ':rawReceivePower:AVERAGE ';
    $rrd_options .= " LINE2:rawReceivePower#00FF00:'Receive Power         ' ";
    $rrd_options .= ' GPRINT:rawReceivePower:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:rawReceivePower:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:rawReceivePower:MAX:%0.2lf%s\\\l ';
}
