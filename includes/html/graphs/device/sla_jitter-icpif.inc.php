<?php
$sla = dbFetchRow('SELECT `sla_nr` FROM `slas` WHERE `sla_id` = ?', [$vars['id']]);

require 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -E ';
$rrd_filename = Rrd::name($device['hostname'], ['sla', $sla['sla_nr'], 'jitter']);

if (Rrd::checkRrdExists($rrd_filename)) {
    $rrd_options .= " COMMENT:'                           Cur  Min  Max  Avg\\n'";

    $rrd_options .= ' DEF:ICPIF=' . $rrd_filename . ':ICPIF:AVERAGE ';
    $rrd_options .= " LINE1.25:ICPIF#0000ee:'ICPIF                ' ";
    $rrd_options .= ' GPRINT:ICPIF:LAST:%3.0lf ';
    $rrd_options .= ' GPRINT:ICPIF:MIN:%3.0lf ';
    $rrd_options .= ' GPRINT:ICPIF:MAX:%3.0lf ';
    $rrd_options .= " GPRINT:ICPIF:AVERAGE:'%3.0lf'\\\l ";
}
