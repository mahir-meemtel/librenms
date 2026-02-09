<?php
use App\Models\Eventlog;
use ObzoraNMS\Exceptions\JsonAppException;
use ObzoraNMS\RRD\RrdDefinition;

$name = 'powermon';

try {
    $result = json_app_get($device, $name);
} catch (JsonAppException $e) {
    echo PHP_EOL . $name . ':' . $e->getCode() . ':' . $e->getMessage() . PHP_EOL;
    update_application($app, $e->getCode() . ':' . $e->getMessage(), []);
    // Set empty metrics and error message
    Eventlog::log('application ' . $name . ' caught JsonAppException');

    return;
}
// should be doing something with error codes/messages returned in the snmp
// result or will they be caught above?

$rrd_def = RrdDefinition::make()
    ->addDataset('watts-gauge', 'GAUGE', 0)
    ->addDataset('watts-abs', 'ABSOLUTE', 0)
    ->addDataset('rate', 'GAUGE', 0);

$fields = [
    'watts-gauge' => $result['data']['reading'],
    'watts-abs' => $result['data']['reading'],
    'rate' => $result['data']['supply']['rate'],
];

/*
Eventlog::log(
      "watts-gauage: " . $result['data']['reading']
    . ", watts-abs: " . $result['data']['reading']
);
 */

$tags = [
    'name' => $name,
    'app_id' => $app->app_id,
    'rrd_name' => ['app', $name, $app->app_id],
    'rrd_def' => $rrd_def,
];
app('Datastore')->put($device, 'app', $tags, $fields);
update_application($app, 'OK', $fields);
