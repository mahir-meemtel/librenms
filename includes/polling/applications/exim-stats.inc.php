<?php
use ObzoraNMS\RRD\RrdDefinition;

//NET-SNMP-EXTEND-MIB::nsExtendOutputFull."exim-stats"
$name = 'exim-stats';
$oid = '.1.3.6.1.4.1.8072.1.3.2.3.1.2.10.101.120.105.109.45.115.116.97.116.115';
$stats = snmp_get($device, $oid, '-Oqv');

[$frozen, $queue] = explode("\n", $stats);

$rrd_def = RrdDefinition::make()
    ->addDataset('frozen', 'GAUGE', 0)
    ->addDataset('queue', 'GAUGE', 0);

$fields = [
    'frozen' => intval(trim($frozen, '"')),
    'queue' => intval(trim($queue, '"')),
];

$tags = [
    'name' => $name,
    'app_id' => $app->app_id,
    'rrd_name' => ['app', $name, $app->app_id],
    'rrd_def' => $rrd_def,
];
app('Datastore')->put($device, 'app', $tags, $fields);
update_application($app, $stats, $fields);
