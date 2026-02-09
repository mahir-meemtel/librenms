<?php
require 'includes/html/graphs/common.inc.php';
$scale_min = 0;
$ds = 'time_remaining';
$colour_area = 'FF000011';
$colour_line = 'FF0000';
$colour_area_max = 'FFEE99';
$graph_max = 0;
$unit_text = 'Minutes';
$ups_apcups = Rrd::name($device['hostname'], ['app', 'ups-apcups', $app->app_id]);
if (Rrd::checkRrdExists($ups_apcups)) {
    $rrd_filename = $ups_apcups;
}
require 'includes/html/graphs/generic_simplex.inc.php';
