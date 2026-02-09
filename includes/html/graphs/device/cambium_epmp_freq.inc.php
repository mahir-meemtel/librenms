<?php
require 'includes/html/graphs/common.inc.php';
$rrdfilename = Rrd::name($device['hostname'], 'cambium-epmp-freq');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'Mhz         \\n'";
    $rrd_options .= ' DEF:freq=' . $rrdfilename . ':freq:AVERAGE ';
    $rrd_options .= " LINE2:freq#008080:'Frequency  ' ";
    $rrd_options .= ' GPRINT:freq:LAST:%0.2lf%s ';
}
