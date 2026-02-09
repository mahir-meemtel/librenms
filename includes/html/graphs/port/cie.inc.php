<?php
$cisco_if_extension_ds = [
    'InRuntsErrs' => 'In Runts',
    'InGiantsErrs' => 'In Giants',
    'InFramingErrs' => 'In Framing err',
    'InOverrunErrs' => 'In Overruns',
    'InIgnored' => 'In Ignored',
    'InAbortErrs' => 'In Aborts',
    'InputQueueDrops' => 'In Queue drops',
    'OutputQueueDrops' => 'Out Queue drops',
];

$i = 0;
$rrd_filename = get_port_rrdfile_path($device['hostname'], $port['port_id'], 'cie');

if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($cisco_if_extension_ds as $ds => $descr) {
        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = $descr;
        $rrd_list[$i]['ds'] = $ds;
        $i++;
    }
}

$colours = 'mixed';
$nototal = 1;
$unit_text = 'Errors/sec';
$simple_rrd = 1;

require 'includes/html/graphs/generic_multi_simplex_seperated.inc.php';
