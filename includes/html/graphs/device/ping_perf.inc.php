<?php
$filename = Rrd::name($device['hostname'], 'icmp-perf');

$descr = 'Milliseconds';
$ds = 'avg';
$scale_min = 0;

require 'includes/html/graphs/generic_stats.inc.php';
