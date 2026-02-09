<?php
$component = new ObzoraNMS\Component();
$components = $component->getComponents($device['device_id'], ['type' => 'cisco-qfp']);
$components = $components[$device['device_id']];

/*
 * Iterate over QFP components and create rrd_list array entry for each of them
 */
$i = 1;
foreach ($components as $component_id => $tmp_component) {
    $rrd_filename = Rrd::name($device['hostname'], ['cisco-qfp', 'util', $tmp_component['entPhysicalIndex']]);

    if (Rrd::checkRrdExists($rrd_filename)) {
        $descr = short_hrDeviceDescr($tmp_component['name']);

        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = $descr;
        $rrd_list[$i]['ds'] = 'ProcessingLoad';
        $rrd_list[$i]['area'] = 1;
        $i++;
    }
}

$unit_text = 'Util %';

$units = '';
$total_units = '%';
$colours = 'mixed';

$scale_min = '0';
$scale_max = '100';

$nototal = 1;

require 'includes/html/graphs/generic_multi_line.inc.php';
