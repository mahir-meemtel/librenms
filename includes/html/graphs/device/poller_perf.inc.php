<?php
$filename = Rrd::name($device['hostname'], 'poller-perf');

$descr = 'Seconds';
$ds = 'poller';

require 'includes/html/graphs/generic_stats.inc.php';
