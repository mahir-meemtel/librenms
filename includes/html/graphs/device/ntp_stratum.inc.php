<?php
$component = new ObzoraNMS\Component();
$options = [];
$options['filter']['type'] = ['=', 'ntp'];
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

include 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -u 16 -E ';
$rrd_options .= " COMMENT:'Stratum               Now      Min      Max\\n'";
$rrd_additions = '';

$count = 0;
foreach ($components as $id => $array) {
    $rrd_filename = Rrd::name($device['hostname'], ['ntp', $array['peer']]);

    if (Rrd::checkRrdExists($rrd_filename)) {
        // Grab a color from the array.
        $color = \App\Facades\ObzoraConfig::get("graph_colours.mixed.$count", \App\Facades\ObzoraConfig::get('graph_colours.oranges.' . ($count - 7)));

        $rrd_additions .= ' DEF:DS' . $count . '=' . $rrd_filename . ':stratum:AVERAGE ';
        $rrd_additions .= ' LINE1.25:DS' . $count . '#' . $color . ":'" . str_pad(substr($array['peer'], 0, 15), 15) . "'" . $stack;
        $rrd_additions .= ' GPRINT:DS' . $count . ':LAST:%5.0lf ';
        $rrd_additions .= ' GPRINT:DS' . $count . ':MIN:%7.0lf ';
        $rrd_additions .= ' GPRINT:DS' . $count . ':MAX:%7.0lf\\l ';
        $count++;
    }
}

if ($rrd_additions == '') {
    // We didn't add any data points.
} else {
    $rrd_options .= $rrd_additions;
}
