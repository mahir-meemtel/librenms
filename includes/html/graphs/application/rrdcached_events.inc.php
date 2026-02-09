<?php
include 'rrdcached.inc.php';

$nototal = 1;
$colours = 'mixed';
$descr_len = 16;

$rrd_list = [
    [
        'ds' => 'updates_written',
        'filename' => $rrd_filename,
        'descr' => 'Updates Written',
    ],
    [
        'ds' => 'data_sets_written',
        'filename' => $rrd_filename,
        'descr' => 'Data Sets Written',
    ],
    [
        'ds' => 'updates_received',
        'filename' => $rrd_filename,
        'descr' => 'Updates Received',
    ],
    [
        'ds' => 'flushes_received',
        'filename' => $rrd_filename,
        'descr' => 'Flushes Received',
    ],
];

require 'includes/html/graphs/generic_multi_line.inc.php';
