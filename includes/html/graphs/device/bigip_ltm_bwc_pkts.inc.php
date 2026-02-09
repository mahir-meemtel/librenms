<?php
$component = new ObzoraNMS\Component();
$options = [];
$options['filter']['type'] = ['=', 'f5-ltm-bwc'];
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

// Is the ID we are looking for a valid LTM VS
if (isset($components[$vars['id']])) {
    $label = $components[$vars['id']]['label'];
    $hash = $components[$vars['id']]['hash'];

    include 'includes/html/graphs/common.inc.php';
    $rrd_options .= ' -l 0 -E ';
    $rrd_options .= " COMMENT:'Bits           Now      Ave      Max\\n'";

    $rrd_filename = Rrd::name($device['hostname'], ['f5-ltm-bwc', $label, $hash]);
    if (Rrd::checkRrdExists($rrd_filename)) {
        $rrd_options .= ' DEF:DS=' . $rrd_filename . ':pktsin:AVERAGE ';
        $rrd_options .= " LINE1.25:DS#205F9A:'Packets In '";
        $rrd_options .= ' GPRINT:DS:LAST:%6.2lf%s ';
        $rrd_options .= ' GPRINT:DS:AVERAGE:%6.2lf%s ';
        $rrd_options .= " GPRINT:DS:MAX:%6.2lf%s\l ";
    }
}
