<?php
$sla = dbFetchRow('SELECT `sla_nr` FROM `slas` WHERE `sla_id` = ?', [$vars['id']]);

require 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -E ';
$rrd_filename = Rrd::name($device['hostname'], ['sla', $sla['sla_nr'], 'jitter']);

if (Rrd::checkRrdExists($rrd_filename)) {
    $rrd_options .= " COMMENT:'                           Cur   Min   Max   Avg\\n'";

    $rrd_options .= ' DEF:MOS=' . $rrd_filename . ':MOS:AVERAGE ';
    $rrd_options .= " LINE1.25:MOS#0000ee:'Mean Opinion Score   ' ";
    $rrd_options .= ' GPRINT:MOS:LAST:%3.2lf ';
    $rrd_options .= ' GPRINT:MOS:MIN:%3.2lf ';
    $rrd_options .= ' GPRINT:MOS:MAX:%3.2lf ';
    $rrd_options .= " GPRINT:MOS:AVERAGE:'%3.2lf'\\\l ";
}
