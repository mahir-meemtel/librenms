<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$ds = 'mode';
$colour_area = 'FFCECE';
$colour_line = '880000';
$colour_area_max = 'FFCCCC';
$graph_max = 0;
$unit_text = 'Mode';

$gpsd = Rrd::name($device['hostname'], ['app', 'gpsd', $app->app_id]);
$rrd_filename = $gpsd;

require 'includes/html/graphs/generic_simplex.inc.php';
