<?php
use ObzoraNMS\Exceptions\JsonAppException;
use ObzoraNMS\Exceptions\JsonAppParsingFailedException;
use ObzoraNMS\RRD\RrdDefinition;

$name = 'gpsd';

if ($app->app_id > 0) {
    if (! empty($agent_data['app'][$name])) {
        $gpsd = $agent_data['app'][$name];

        $gpsd_parsed = [];

        foreach (explode("\n", $gpsd) as $line) {
            [$field, $data] = explode(':', $line);
            $gpsd_parsed[$field] = $data;
        }

        // Set Fields

        $check_fields = [
            'mode',
            'hdop',
            'vdop',
            'satellites',
            'satellites_used',
        ];

        $fields = [];

        foreach ($check_fields as $field) {
            if (! empty($gpsd_parsed[$field])) {
                $fields[$field] = $gpsd_parsed[$field];
            }
        }
    } else {
        // Use json_app_get to grab JSON formatted GPSD data
        try {
            $gpsd = json_app_get($device, $name);
        } catch (JsonAppParsingFailedException $e) {
            $legacy = $e->getOutput();

            $gpsd = [
                'data' => [],
            ];

            [$gpsd['data']['mode'], $gpsd['data']['hdop'], $gpsd['data']['vdop'],
                $gpsd['data']['latitude'], $gpsd['data']['longitude'], $gpsd['data']['altitude'],
                $gpsd['data']['satellites'], $gpsd['data']['satellites_used']] = explode("\n", $legacy);
        } catch (JsonAppException $e) {
            // Set Empty metrics and error message

            echo PHP_EOL . $name . ':' . $e->getCode() . ':' . $e->getMessage() . PHP_EOL;
            update_application($app, $e->getCode() . ':' . $e->getMessage(), []);

            return;
        }

        // Set Fields
        $fields = [
            'mode' => $gpsd['data']['mode'],
            'hdop' => $gpsd['data']['hdop'],
            'vdop' => $gpsd['data']['vdop'],
            'satellites' => $gpsd['data']['satellites'],
            'satellites_used' => $gpsd['data']['satellites_used'],
        ];
    }

    // Generate RRD Def

    $rrd_def = RrdDefinition::make()
        ->addDataset('mode', 'GAUGE', 0, 4)
        ->addDataset('hdop', 'GAUGE', 0, 100)
        ->addDataset('vdop', 'GAUGE', 0, 100)
        ->addDataset('satellites', 'GAUGE', 0, 40)
        ->addDataset('satellites_used', 'GAUGE', 0, 40);

    // Update Application
    $tags = [
        'name' => $name,
        'app_id' => $app->app_id,
        'rrd_name' => ['app', $name, $app->app_id],
        'rrd_def' => $rrd_def,
    ];
    app('Datastore')->put($device, 'app', $tags, $fields);

    if (! empty($agent_data['app'][$name])) {
        update_application($app, $gpsd, $fields);
    } else {
        update_application($app, 'OK', $fields);
    }
}
