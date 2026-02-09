<?php
use App\Models\DiskIo;
use App\Models\Mempool;
use App\Models\Processor;
use App\Models\Sensor;
use App\Models\Storage;

/*
# QFP count for cisco devices
*/

$qfp = 0;
if ($device['os_group'] == 'cisco') {
    $component = new ObzoraNMS\Component();
    $components = $component->getComponents($device['device_id'], ['type' => 'cisco-qfp']);
    $components = $components[$device['device_id']];
    $qfp = isset($components) ? count($components) : 0;
}

unset($datas);
$datas[] = 'overview';

if (Processor::where('device_id', $device['device_id'])->exists()) {
    $datas[] = 'processor';
}

if ($qfp) {
    $datas[] = 'qfp';
}

if (Mempool::where('device_id', $device['device_id'])->exists()) {
    $datas[] = 'mempool';
}

if (Storage::where('device_id', $device['device_id'])->exists()) {
    $datas[] = 'storage';
}

if (DiskIo::where('device_id', $device['device_id'])->exists()) {
    $datas[] = 'diskio';
}

foreach (Sensor::where('device_id', $device['device_id'])->distinct()->pluck('sensor_class') as $sensor_class) {
    $datas[] = $sensor_class;
    $type_text[$sensor_class] = trans('sensors.' . $sensor_class . '.short');
}

$type_text['overview'] = 'Overview';
$type_text['qfp'] = 'QFP';
$type_text['processor'] = 'Processor';
$type_text['mempool'] = 'Memory';
$type_text['storage'] = 'Disk Usage';
$type_text['diskio'] = 'Disk I/O';

$link_array = [
    'page' => 'device',
    'device' => $device['device_id'],
    'tab' => 'health',
];

print_optionbar_start();

echo "<span style='font-weight: bold;'>Health</span> &#187; ";

if (empty($vars['metric'])) {
    $vars['metric'] = 'overview';
}

$sep = '';
foreach ($datas as $type) {
    echo $sep;
    if ($vars['metric'] == $type) {
        echo '<span class="pagemenu-selected">';
    }

    echo generate_link($type_text[$type], $link_array, ['metric' => $type]);
    if ($vars['metric'] == $type) {
        echo '</span>';
    }

    $sep = ' | ';
}

print_optionbar_end();

$metric = basename($vars['metric']);
if (is_file("includes/html/pages/device/health/$metric.inc.php")) {
    include "includes/html/pages/device/health/$metric.inc.php";
} else {
    foreach ($datas as $type) {
        if ($type != 'overview') {
            $graph_title = $type_text[$type];
            $graph_array['type'] = 'device_' . $type;
            include 'includes/html/print-device-graph.php';
        }
    }
}

$pagetitle[] = 'Health';
