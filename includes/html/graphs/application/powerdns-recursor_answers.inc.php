<?php
include 'powerdns-recursor.inc.php';

$colours = 'oranges';
$unit_text = 'Answers/sec';
$print_total = true;

$rrd_list = [
    [
        'ds' => 'answers0-1',
        'filename' => $rrd_filename,
        'descr' => '0-1ms',
    ],
    [
        'ds' => 'answers1-10',
        'filename' => $rrd_filename,
        'descr' => '1-10ms',
    ],
    [
        'ds' => 'answers10-100',
        'filename' => $rrd_filename,
        'descr' => '10-100ms',
    ],
    [
        'ds' => 'answers100-1000',
        'filename' => $rrd_filename,
        'descr' => '100-1000ms',
    ],
    [
        'ds' => 'answers-slow',
        'filename' => $rrd_filename,
        'descr' => '>1s',
    ],
];

require 'includes/html/graphs/generic_multi.inc.php';
