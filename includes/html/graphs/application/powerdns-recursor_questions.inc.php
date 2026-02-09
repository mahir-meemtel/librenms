<?php
include 'powerdns-recursor.inc.php';

$colours = 'mixed';
$unit_text = 'Questions/sec';

$rrd_list = [
    [
        'filename' => $rrd_filename,
        'ds' => 'questions',
        'descr' => 'Total Questions',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'ipv6-questions',
        'descr' => 'IPv6 Questions',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'tcp-questions',
        'descr' => 'TCP Questions',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'over-capacity-drops',
        'descr' => 'Over Capacity Drops',
        'area' => true,
    ],
    [
        'filename' => $rrd_filename,
        'ds' => 'policy-drops',
        'descr' => 'Policy Drops',
        'area' => true,
    ],
];

require 'includes/html/graphs/generic_multi_line.inc.php';
