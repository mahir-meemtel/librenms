<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$ds = 'charge';
$colour_area = 'FF330011';
$colour_line = 'FF3300';
$colour_area_max = 'FFEE99';
$graph_max = 0;
$unit_text = 'Percent';
$ups_apcups = Rrd::name($device['hostname'], ['app', 'ups-apcups', $app->app_id]);
if (Rrd::checkRrdExists($ups_apcups)) {
    $rrd_filename = $ups_apcups;
}
require 'includes/html/graphs/generic_simplex.inc.php';
