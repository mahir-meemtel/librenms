<?php
require 'includes/html/graphs/common.inc.php';

$i = 0;
$scale_min = 0;
$nototal = 1;
$unit_text = 'Query/sec';
$rrd_filename = Rrd::name($device['hostname'], ['app', 'tinydns', $app->app_id]);
// $array        = explode(":","hinfo:rp:sig:key:axfr:total");
$array = [
    'key',
    'sig',
];
$colours = 'mixed';
$rrd_list = [];

if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($array as $ds) {
        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = strtoupper($ds);
        $rrd_list[$i]['ds'] = $ds;
        $i++;
    }
} else {
    throw new \ObzoraNMS\Exceptions\RrdGraphException("No Data file $rrd_filename");
}

require 'includes/html/graphs/generic_multi_simplex_seperated.inc.php';
