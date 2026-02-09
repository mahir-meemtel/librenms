<?php
header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'You need to be admin',
    ]));
}

$status = 'ok';
$message = '';

$transport_id = strip_tags($vars['transport_id']);
$name = strip_tags($vars['name']);
$is_default = (int) (isset($vars['is_default']) && $vars['is_default'] == 'on');
$transport_type = strip_tags($vars['transport-type']);

if (empty($name)) {
    $status = 'error';
    $message = 'No transport name provided';
} elseif (empty($transport_type)) {
    $status = 'error';
    $message = 'Missing transport information';
} else {
    $details = [
        'transport_name' => $name,
        'is_default' => $is_default,
    ];

    if (is_numeric($transport_id) && $transport_id > 0) {
        // Update the fields -- json config field will be updated later
        dbUpdate($details, 'alert_transports', 'transport_id=?', [$transport_id]);
    } else {
        // Insert the new alert transport
        $newEntry = true;
        $transport_id = dbInsert($details, 'alert_transports');
    }

    if ($transport_id) {
        $class = 'ObzoraNMS\\Alert\\Transport\\' . ucfirst($transport_type);

        if (! method_exists($class, 'configTemplate')) {
            exit(json_encode([
                'status' => 'error',
                'message' => 'This transport type is not yet supported',
            ]));
        }

        // Build config values
        $result = call_user_func_array($class . '::configTemplate', []);
        $validator = Validator::make($vars, $result['validation']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                $message .= "$error<br>";
            }
            $status = 'error';
        } else {
            $transport_config = (array) json_decode(dbFetchCell('SELECT transport_config FROM alert_transports WHERE transport_id=?', [$transport_id]), true);
            foreach ($result['config'] as $tmp_config) {
                if (isset($tmp_config['name']) && $tmp_config['type'] !== 'hidden') {
                    $transport_config[$tmp_config['name']] = $vars[$tmp_config['name']] ?? null;
                }
            }
            //Update the json config field
            $detail = [
                'transport_type' => $transport_type,
                'transport_config' => json_encode($transport_config),
            ];
            $where = 'transport_id=?';

            dbUpdate($detail, 'alert_transports', $where, [$transport_id]);

            $status = 'ok';
            $message = 'Updated alert transports';
        }
        if ($status == 'error' && $newEntry) {
            //If error, we will have to delete the new entry in alert_transports tbl
            $where = '`transport_id`=?';
            dbDelete('alert_transports', $where, [$transport_id]);
        }
    } else {
        $status = 'error';
        $message = 'Failed to update transport';
    }
}

exit(json_encode([
    'status' => $status,
    'message' => $message,
]));
