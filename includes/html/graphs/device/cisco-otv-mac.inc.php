<?php
$component = new ObzoraNMS\Component();
$options['filter']['type'] = ['=', 'Cisco-OTV'];
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

include 'includes/html/graphs/common.inc.php';
$rrd_options .= ' -l 0 -E ';
$rrd_options .= " COMMENT:'MAC Addresses       Now    Min     Max\\n'";
$rrd_additions = '';

$count = 0;
foreach ($components as $id => $array) {
    if ($array['otvtype'] == 'endpoint') {
        $rrd_filename = Rrd::name($device['hostname'], ['cisco', 'otv', $array['endpoint'], 'mac']);

        if (Rrd::checkRrdExists($rrd_filename)) {
            // Stack the area on the second and subsequent DS's
            $stack = '';
            if ($count != 0) {
                $stack = ':STACK ';
            }

            // Grab a color from the array.
            $color = \App\Facades\ObzoraConfig::get("graph_colours.mixed.$count", \App\Facades\ObzoraConfig::get('graph_colours.oranges.' . ($count - 7)));

            $rrd_additions .= ' DEF:DS' . $count . '=' . $rrd_filename . ':count:AVERAGE ';
            $rrd_additions .= ' AREA:DS' . $count . '#' . $color . ":'" . str_pad(substr($components[$id]['endpoint'], 0, 15), 15) . "'" . $stack;
            $rrd_additions .= ' GPRINT:DS' . $count . ':LAST:%4.0lf%s ';
            $rrd_additions .= ' GPRINT:DS' . $count . ':MIN:%4.0lf%s ';
            $rrd_additions .= ' GPRINT:DS' . $count . ":MAX:%4.0lf%s\\\l ";
            $count++;
        }
    }
}

if ($rrd_additions == '') {
    // We didn't add any data points.
} else {
    $rrd_options .= $rrd_additions;
}
