<?php
include 'powerdns-recursor.inc.php';

$colours = 'purples';
$unit_text = 'Entries';

$rrd_list = [
    [
        'filename' => $rrd_filename,
        'ds' => 'cache-entries',
        'descr' => 'Query Cache',
        'colour' => '202048',
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'packetcache-entries',
        'descr' => 'Packet Cache',
        'colour' => 'CC7CCC',
    ],
];

require 'includes/html/graphs/generic_multi_line.inc.php';
