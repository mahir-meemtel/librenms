<?php
use ObzoraNMS\RRD\RrdDefinition;

$name = 'ogs';
$oid = '.1.3.6.1.4.1.8072.1.3.2.3.1.2.3.111.103.115';

echo ' ' . $name;

// get data through snmp
$ogs_data = snmp_get($device, $oid, '-Oqv');

// define the rrd
$rrd_def = RrdDefinition::make()
    ->addDataset('running_jobs', 'GAUGE', 0)
    ->addDataset('pending_jobs', 'GAUGE', 0)
    ->addDataset('suspend_jobs', 'GAUGE', 0)
    ->addDataset('zombie_jobs', 'GAUGE', 0);

// parse the data from the script
$data = explode("\n", $ogs_data);
$fields = [
    'running_jobs' => $data[0],
    'pending_jobs' => $data[1],
    'suspend_jobs' => $data[2],
    'zombie_jobs' => $data[3],
];

// push the data in an array and into the rrd
$tags = [
    'name' => $name,
    'app_id' => $app->app_id,
    'rrd_name' => ['app', $name, $app->app_id],
    'rrd_def' => $rrd_def,
];
app('Datastore')->put($device, 'app', $tags, $fields);
update_application($app, $ogs_data, $fields);

// cleanup
unset($ogs_data, $rrd_def, $data, $fields, $tags);
