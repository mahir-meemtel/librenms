<?php
include 'powerdns-recursor.inc.php';

$colours = 'mixed';
$unit_text = 'Queries/sec';

$rrd_list = [
    [
        'filename' => $rrd_filename,
        'ds' => 'all-outqueries',
        'descr' => 'Total',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'ipv6-outqueries',
        'descr' => 'IPv6',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'tcp-outqueries',
        'descr' => 'TCP',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'throttled-out',
        'descr' => 'Throttled',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'outgoing-timeouts',
        'descr' => 'Timeouts',
        'area' => true,
    ],
];

require 'includes/html/graphs/generic_multi_line.inc.php';
