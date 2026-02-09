<?php
$name = 'powermon';
$rrd_filename = Rrd::name($device['hostname'], ['app', $name, $app->app_id]);

$ds_list[0]['vname'] = 'watts';
$ds_list[0]['ds'] = 'watts-gauge';
$ds_list[0]['filename'] = $rrd_filename;
$ds_list[0]['descr'] = 'Average Power';
$ds_list[0]['units_text'] = ' W';
$ds_list[0]['colour'] = 'FF6600'; // orange
$ds_list[0]['colour'] = 'F9F900'; // yellow
$ds_list[0]['areacolour'] = 'FAFAB2'; // yellow

$ds_list[1]['vname'] = 'rate';
$ds_list[1]['ds'] = 'rate';
$ds_list[1]['filename'] = $rrd_filename;
$ds_list[1]['descr'] = '  Total Cost';
$ds_list[1]['units_text'] = '$';
$ds_list[1]['colour'] = '006600'; // money green

// COMMON OPTIONS

//$from = ;
//$to = ;
//$width = 200;
//$height = 100;
//$inverse = false;
//$nototal = true;
//$nodetails = false;
//$noagg = true;
//$title = '';
$scale_min = 0;
//$scale_max = 0;
//$scale_rigid = anything;
//$norigid = false;
$float_precision = 0;

// LOCAL OPTIONS

$line_width = 2.5;
$pad_to = 18;               // padding for left-hand column in legend
$currency_symbol = '$';     // update this if required

require 'includes/html/graphs/common.inc.php';

//if ($nototal) {
//    $pad_to += '2';
//}

$rrd_options .= ' COMMENT:\s'; // spacer in legend

$i = 0;
foreach ($ds_list as $ds_item) {
    $vname = $ds_item['vname'];
    $ds = $ds_item['ds'];
    $filename = $ds_item['filename'];
    $descr = \ObzoraNMS\Data\Store\Rrd::fixedSafeDescr($ds_item['descr'], $pad_to);

    // CF to use
    $use_cf_last = ['nothing', 'nothing'];

    if (in_array($vname, $use_cf_last)) {
        $cf = 'LAST';
    } else {
        $cf = 'AVERAGE';
    }

    $rrd_options .= ' DEF:' . "$vname=$filename:$ds:" . $cf;

    // Units
    if (isset($ds_item['units_text'])) {
        $units_text = $ds_item['units_text'];
    } else {
        $units_text = '';
    }

    // Line Width
    if (isset($ds_item['line_width'])) {
        $ds_line_width = $ds_item['line_width'];
    } else {
        $ds_line_wdith = $line_width;
    }

    // Line Colour
    if (isset($ds_item['colour'])) {
        $colour = $ds_item['colour'];
    } else {
        if (! \App\Facades\ObzoraConfig::get("graph_colours.$colours.$i")) {
            $i = 0;
        }
        $colour = \App\Facades\ObzoraConfig::get("graph_colours.$colours.$i");
        $i++;
    }

    // Area Colour
    if (isset($ds_item['areacolour'])) {
        $areacolour = $ds_item['areacolour'];
    } else {
        $areacolour = $colour . '20';
    }

    // Graph command

    if ($vname == 'watts') {
        $rrd_options .= ' AREA:' . $vname . '#' . $areacolour;
        $rrd_options .= " LINE{$ds_line_width}:{$vname}#{$colour}:'{$descr}'";
        $rrd_options .= " GPRINT:{$vname}:AVERAGE:%12.{$float_precision}lf'{$units_text}'\l";

        $rrd_options .= ' COMMENT:\s'; // spacer in legend
        /*
                // Watt Seconds
                $descr = '  Total Consumed';
                $units_text = ' Ws';
                $descr = rrdtool_escape($descr, $pad_to + 2);
                $rrd_options .= " COMMENT:'{$descr}'";
                $rrd_options .= ' VDEF:wattsecs=watts,TOTAL';
                $rrd_options .= " GPRINT:wattsecs:%12.{$float_precision}lf'{$units_text}'\l";
        */
        // Kilowatt Hours
        $units_text = ' kWh';
        $float_precision = 2;
        $descr = '  Total Consumed';
        $descr = \ObzoraNMS\Data\Store\Rrd::fixedSafeDescr($descr, $pad_to + 2);
        $rrd_options .= " COMMENT:'{$descr}'";
        $rrd_options .= ' CDEF:series_a=watts,3600000,/';
        $rrd_options .= ' VDEF:kilowatthours=series_a,TOTAL';
        $rrd_options .= " GPRINT:kilowatthours:%12.{$float_precision}lf'{$units_text}'\l";
    } elseif ($vname == 'rate') {
        // Consumption Charge
        $float_precision = 2;
        $descr = \ObzoraNMS\Data\Store\Rrd::fixedSafeDescr($descr, $pad_to + 7);
        $rrd_options .= " COMMENT:'{$descr}{$currency_symbol}'";
        $rrd_options .= " CDEF:series_b=watts,{$vname},*,3600000,/";
        $rrd_options .= ' VDEF:total_cost=series_b,TOTAL';
        $rrd_options .= " GPRINT:total_cost:%6.{$float_precision}lf'           @ average rate of'";
        $rrd_options .= ' VDEF:average_rate=rate,AVERAGE';
        $rrd_options .= " GPRINT:average_rate:%0.6lf' per kWh\l'";
    }
}
