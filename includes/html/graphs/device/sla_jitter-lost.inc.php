<?php
$sla = dbFetchRow('SELECT `sla_nr` FROM `slas` WHERE `sla_id` = ?', [$vars['id']]);

require 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -E ';
$rrd_filename = Rrd::name($device['hostname'], ['sla', $sla['sla_nr'], 'jitter']);

if (Rrd::checkRrdExists($rrd_filename)) {
    $rrd_options .= " COMMENT:'                          Cur  Min  Max  Avg\\n'";

    $rrd_options .= ' DEF:POS=' . $rrd_filename . ':PacketOutOfSequence:AVERAGE ';
    $rrd_options .= " LINE1.25:POS#0000ee:'Out of Sequence      ' ";
    $rrd_options .= ' GPRINT:POS:LAST:%3.0lf ';
    $rrd_options .= ' GPRINT:POS:MIN:%3.0lf ';
    $rrd_options .= ' GPRINT:POS:MAX:%3.0lf ';
    $rrd_options .= " GPRINT:POS:AVERAGE:'%3.0lf'\\\l ";

    $rrd_options .= ' DEF:MIA=' . $rrd_filename . ':PacketMIA:AVERAGE ';
    $rrd_options .= " LINE1.25:MIA#008C00:'Missing in Action    ' ";
    $rrd_options .= ' GPRINT:MIA:LAST:%3.0lf ';
    $rrd_options .= ' GPRINT:MIA:MIN:%3.0lf ';
    $rrd_options .= ' GPRINT:MIA:MAX:%3.0lf ';
    $rrd_options .= " GPRINT:MIA:AVERAGE:'%3.0lf'\\\l ";

    $rrd_options .= ' DEF:PLA=' . $rrd_filename . ':PacketLateArrival:AVERAGE ';
    $rrd_options .= " LINE1.25:PLA#CC0000:'Late Arrival         ' ";
    $rrd_options .= ' GPRINT:PLA:LAST:%3.0lf ';
    $rrd_options .= ' GPRINT:PLA:MIN:%3.0lf ';
    $rrd_options .= ' GPRINT:PLA:MAX:%3.0lf ';
    $rrd_options .= " GPRINT:PLA:AVERAGE:'%3.0lf'\\\l ";
}
