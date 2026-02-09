<?php
include 'rrdcached.inc.php';

$colours = 'pinks';

$rrd_list = [
    [
        'ds' => 'journal_rotate',
        'filename' => $rrd_filename,
        'descr' => 'Journal Rotated',
    ],
    [
        'ds' => 'journal_bytes',
        'filename' => $rrd_filename,
        'descr' => 'Journal Bytes Written',
    ],
];

require 'includes/html/graphs/generic_multi.inc.php';
