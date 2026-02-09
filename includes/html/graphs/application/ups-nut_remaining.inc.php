<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$ds = 'time_remaining';
$colour_area = 'EEEEEE';
$colour_line = '36393D';
$colour_area_max = 'FFEE99';
$graph_max = 0;
$unit_text = 'Minutes';
$ups_nut = Rrd::name($device['hostname'], ['app', 'ups-nut', $app->app_id]);
$rrd_filename = $ups_nut;

require 'includes/html/graphs/generic_simplex.inc.php';
