<?php
use ObzoraNMS\Exceptions\JsonAppException;
use ObzoraNMS\RRD\RrdDefinition;

$name = 'mailcow-postfix';

try {
    $mailcow_postfix = json_app_get($device, $name);
} catch (JsonAppException $e) {
    echo PHP_EOL . $name . ':' . $e->getCode() . ':' . $e->getMessage() . PHP_EOL;
    update_application($app, $e->getCode() . ':' . $e->getMessage(), []); // Set empty metrics and error message

    return;
}

$rrd_def = RrdDefinition::make()
    ->addDataset('received', 'GAUGE', 0)
    ->addDataset('delivered', 'GAUGE', 0)
    ->addDataset('forwarded', 'GAUGE', 0)
    ->addDataset('deferred', 'GAUGE', 0)
    ->addDataset('bounced', 'GAUGE', 0)
    ->addDataset('rejected', 'GAUGE', 0)
    ->addDataset('rejectwarnings', 'GAUGE', 0)
    ->addDataset('held', 'GAUGE', 0)
    ->addDataset('discarded', 'GAUGE', 0)
    ->addDataset('bytesreceived', 'GAUGE', 0)
    ->addDataset('bytesdelivered', 'GAUGE', 0)
    ->addDataset('senders', 'GAUGE', 0)
    ->addDataset('sendinghostsdomains', 'GAUGE', 0)
    ->addDataset('recipients', 'GAUGE', 0)
    ->addDataset('recipienthostsdomains', 'GAUGE', 0);

$fields = [
    'received' => $mailcow_postfix['data']['received'],
    'delivered' => $mailcow_postfix['data']['delivered'],
    'forwarded' => $mailcow_postfix['data']['forwarded'],
    'deferred' => $mailcow_postfix['data']['deferred'],
    'bounced' => $mailcow_postfix['data']['bounced'],
    'rejected' => $mailcow_postfix['data']['rejected'],
    'rejectwarnings' => $mailcow_postfix['data']['rejectwarnings'],
    'held' => $mailcow_postfix['data']['held'],
    'discarded' => $mailcow_postfix['data']['discarded'],
    'bytesreceived' => $mailcow_postfix['data']['bytesreceived'],
    'bytesdelivered' => $mailcow_postfix['data']['bytesdelivered'],
    'senders' => $mailcow_postfix['data']['senders'],
    'sendinghostsdomains' => $mailcow_postfix['data']['sendinghostsdomains'],
    'recipients' => $mailcow_postfix['data']['recipients'],
    'recipienthostsdomains' => $mailcow_postfix['data']['recipienthostsdomains'],
];

$tags = [
    'name' => $name,
    'app_id' => $app->app_id,
    'rrd_name' => ['app', $name, $app->app_id],
    'rrd_def' => $rrd_def,
];
app('Datastore')->put($device, 'app', $tags, $fields);
update_application($app, 'OK', $fields);
