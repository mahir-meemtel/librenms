<?php
require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$ds = 'net_tcpconn';
$colour_area = '9DDA52';
$colour_line = '2EAC6D';
$colour_area_max = 'FFEE99';
$graph_max = 10000;
$unit_text = 'net tcp connections';

$rrd_filename = Rrd::name($device['hostname'], ['app', 'nfs-server-default', $app->app_id]);

require 'includes/html/graphs/generic_simplex.inc.php';
