<?php
$component = new ObzoraNMS\Component();
$options = [];
$options['filter']['type'] = ['=', 'f5-ltm-vs'];
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

// Is the ID we are looking for a valid LTM VS
if (isset($components[$vars['id']])) {
    $label = $components[$vars['id']]['label'];
    $hash = $components[$vars['id']]['hash'];

    $rrd_filename = Rrd::name($device['hostname'], ['f5-ltm-vs', $label, $hash, 'currconns']);
    if (Rrd::checkRrdExists($rrd_filename)) {
        require 'includes/html/graphs/common.inc.php';
        $ds = 'currconns';

        $colour_area = '9999cc';
        $colour_line = '0000cc';

        $colour_area_max = '9999cc';

        $graph_max = 1;

        $unit_text = 'Current Connections';
        $line_text = 'Current Connections';
        require 'includes/html/graphs/generic_simplex.inc.php';
    }
}
