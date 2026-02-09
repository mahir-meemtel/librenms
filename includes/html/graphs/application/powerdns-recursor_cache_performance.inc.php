<?php
include 'powerdns-recursor.inc.php';

$colours = 'mixed';
$unit_text = 'Packets/sec';

$rrd_list = [
    [
        'filename' => $rrd_filename,
        'ds' => 'cache-hits',
        'descr' => 'Query Cache Hits',
        'colour' => '297159',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'cache-misses',
        'descr' => 'Query Cache Misses',
        'colour' => '73AC61',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'packetcache-hits',
        'descr' => 'Packet Cache Hits',
        'colour' => 'BC7049',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'packetcache-misses',
        'descr' => 'Packet Cache Misses',
        'colour' => 'C98F45',
        'area' => true,
    ],
];

require 'includes/html/graphs/generic_multi_line.inc.php';
