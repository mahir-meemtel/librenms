<?php
use App\Facades\ObzoraConfig;

/*
 * Bind9 Query Graph
 * @author Daniel Preussker <f0o@devilcode.org>
 * @copyright 2015 f0o, ObzoraNMS
 * @license GPL
 * @package ObzoraNMS
 * @subpackage Graphs
 */

require 'includes/html/graphs/common.inc.php';

$i = 0;
$scale_min = 0;
$nototal = 1;
$unit_text = 'Query/sec';
$rrd_filename = Rrd::name($device['hostname'], ['app', 'bind', $app->app_id]);
$array = [
    'any',
    'a',
    'aaaa',
    'cname',
    'mx',
    'ns',
    'ptr',
    'soa',
    'srv',
    'spf',
];
$colours = 'merged';
$rrd_list = [];

ObzoraConfig::set('graph_colours.merged', array_merge(ObzoraConfig::get('graph_colours.greens'), ObzoraConfig::get('graph_colours.blues')));

foreach ($array as $ds) {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = strtoupper($ds);
    $rrd_list[$i]['ds'] = $ds;
    $i++;
}

require 'includes/html/graphs/generic_multi_simplex_seperated.inc.php';
