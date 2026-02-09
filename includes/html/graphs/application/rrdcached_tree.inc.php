<?php
include 'rrdcached.inc.php';

$colours = 'blues';

$rrd_list = [
    [
        'ds' => 'tree_depth',
        'filename' => $rrd_filename,
        'descr' => 'Tree Depth',
    ],
    [
        'ds' => 'tree_nodes_number',
        'filename' => $rrd_filename,
        'descr' => 'Tree Nodes',
    ],
];

require 'includes/html/graphs/generic_multi.inc.php';
