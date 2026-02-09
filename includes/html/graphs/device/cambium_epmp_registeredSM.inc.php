<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'cambium-epmp-registeredSM');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'Value                Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:regSM=' . $rrdfilename . ':regSM:AVERAGE ';
    $rrd_options .= " LINE2:regSM#73b0c2:'Registered SM       ' ";
    $rrd_options .= ' -l 0 ';
    $rrd_options .= ' GPRINT:regSM:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:regSM:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:regSM:MAX:%0.2lf%s\\\l ';
}
