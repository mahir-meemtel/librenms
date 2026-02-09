<?php
$component = new ObzoraNMS\Component();
$options = [];
$options['filter']['type'] = ['=', 'f5-gtm-wide'];
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

include 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -E ';
$rrd_options .= " COMMENT:'Wide IP Resolved Requests                                           Now      Avg      Max\\n'";
$colours = array_merge(\App\Facades\ObzoraConfig::get('graph_colours.mixed'), \App\Facades\ObzoraConfig::get('graph_colours.manycolours'));
$colcount = 0;
$count = 0;

// add all GTM Wide IP Request on this device.
foreach ($components as $compid => $comp) {
    $label = $comp['label'];
    $hash = $comp['hash'];
    $rrd_filename = Rrd::name($device['hostname'], [$comp['type'], $label, $hash]);
    if (Rrd::checkRrdExists($rrd_filename)) {
        // Grab a colour from the array.
        if (isset($colours[$colcount])) {
            $colour = $colours[$colcount];
        } else {
            $colcount = 0;
            $colour = $colours[$colcount];
        }

        $rrd_options .= ' DEF:DS' . $count . '=' . $rrd_filename . ':resolved:AVERAGE ';
        $rrd_options .= ' LINE1.25:DS' . $count . '#' . $colour . ":'" . str_pad(substr($label, 0, 60), 60) . "'";
        $rrd_options .= ' GPRINT:DS' . $count . ':LAST:%6.2lf%s ';
        $rrd_options .= ' GPRINT:DS' . $count . ':AVERAGE:%6.2lf%s ';
        $rrd_options .= ' GPRINT:DS' . $count . ":MAX:%6.2lf%s\l ";
        $count++;
        $colcount++;
    }
}
