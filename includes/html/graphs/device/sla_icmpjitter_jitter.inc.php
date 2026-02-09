<?php
$sla = dbFetchRow('SELECT `sla_nr` FROM `slas` WHERE `sla_id` = ?', [$vars['id']]);

require 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -E ';
$rrd_filename = Rrd::name($device['hostname'], ['sla', $sla['sla_nr'], 'icmpjitter']);

if (Rrd::checkRrdExists($rrd_filename)) {
    $rrd_options .= " COMMENT:'                          Cur    Min    Max    Avg\\n'";

    $rrd_options .= ' DEF:SD=' . $rrd_filename . ':JitterAvgSD:AVERAGE ';
    $rrd_options .= " LINE1.25:SD#0000ee:'Src to Dst (ms)    ' ";
    $rrd_options .= " GPRINT:SD:LAST:'%5.2lf' ";
    $rrd_options .= " GPRINT:SD:MIN:'%5.2lf' ";
    $rrd_options .= " GPRINT:SD:MAX:'%5.2lf' ";
    $rrd_options .= " GPRINT:SD:AVERAGE:'%5.2lf'\\\l ";

    $rrd_options .= ' DEF:DS=' . $rrd_filename . ':JitterAvgDS:AVERAGE ';
    $rrd_options .= " LINE1.25:DS#008C00:'Dst to Src (ms)    ' ";
    $rrd_options .= " GPRINT:DS:LAST:'%5.2lf' ";
    $rrd_options .= " GPRINT:DS:MIN:'%5.2lf' ";
    $rrd_options .= " GPRINT:DS:MAX:'%5.2lf' ";
    $rrd_options .= " GPRINT:DS:AVERAGE:'%5.2lf'\\\l ";
}
