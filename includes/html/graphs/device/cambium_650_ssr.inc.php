<?php
require 'includes/html/graphs/common.inc.php';

$rrdfilename = Rrd::name($device['hostname'], 'cambium-650-ssr');
if (Rrd::checkRrdExists($rrdfilename)) {
    $rrd_options .= " COMMENT:'dBm                      Now       Ave      Max     \\n'";
    $rrd_options .= ' DEF:ssr=' . $rrdfilename . ':ssr:AVERAGE ';
    $rrd_options .= " LINE2:ssr#9B30FF:'Signal Strength Ratio ' ";
    $rrd_options .= ' GPRINT:ssr:LAST:%0.2lf%s ';
    $rrd_options .= ' GPRINT:ssr:MIN:%0.2lf%s ';
    $rrd_options .= ' GPRINT:ssr:MAX:%0.2lf%s\\\l ';
}
