<?php
use ObzoraNMS\Alert\AlertUtil;
use ObzoraNMS\Alerting\QueryBuilderParser;

if (! Auth::user()->hasGlobalAdmin()) {
    echo 'Insufficient Privileges';
    exit;
}

$hostname = escapeshellcmd($_REQUEST['hostname']);
$type = $_REQUEST['type'];

switch ($type) {
    case 'alerts':
        $filename = "alerts-$hostname.txt";
        $device_id = getidbyname($hostname);
        $device = device_by_id_cache($device_id);
        $rules = AlertUtil::getRules($device_id);
        $output = '';
        $results = [];
        foreach ($rules as $rule) {
            if (empty($rule['query'])) {
                $rule['query'] = QueryBuilderParser::fromJson($rule['builder'])->toSql();
            }
            $sql = $rule['query'];
            $qry = dbFetchRow($sql, [$device_id]);
            if (is_array($qry)) {
                $results[] = $qry;
                $response = 'matches';
            } else {
                $response = 'no match';
            }

            $extra = json_decode($rule['extra'], true);
            if ($extra['options']['override_query'] === 'on') {
                $qb = $extra['options']['override_query'];
            } else {
                $qb = QueryBuilderParser::fromJson($rule['builder'] ?? []);
            }

            $output .= 'Rule name: ' . $rule['name'] . PHP_EOL;
            if ($qb instanceof QueryBuilderParser) {
                $output .= 'Alert rule: ' . $qb->toSql(false) . PHP_EOL;
            } else {
                $output .= 'Alert rule: Custom SQL Query' . PHP_EOL;
            }
            $output .= 'Alert query: ' . $rule['query'] . PHP_EOL;
            $output .= 'Rule match: ' . $response . PHP_EOL . PHP_EOL;
        }
        if (\App\Facades\ObzoraConfig::get('alert.transports.mail') === true) {
            $contacts = AlertUtil::getContacts($results);
            if (count($contacts) > 0) {
                $output .= 'Found ' . count($contacts) . ' contacts to send alerts to.' . PHP_EOL;
            }
            foreach ($contacts as $email => $name) {
                $output .= $name . '<' . $email . '>' . PHP_EOL;
            }
            $output .= PHP_EOL;
        }
        $transports = '';
        $x = 0;
        foreach (\App\Facades\ObzoraConfig::get('alert.transports') as $name => $v) {
            if (\App\Facades\ObzoraConfig::get("alert.transports.$name") === true) {
                $transports .= 'Transport: ' . $name . PHP_EOL;
                $x++;
            }
        }
        if (! empty($transports)) {
            $output .= 'Found ' . $x . ' transports to send alerts to.' . PHP_EOL;
            $output .= $transports;
        }
        break;
    default:
        echo 'You must specify a valid type';
        exit;
}

// ---- Output ----

if ($_GET['format'] == 'text') {
    header('Content-type: text/plain');
    header('X-Accel-Buffering: no');

    echo $output;
} elseif ($_GET['format'] == 'download') {
    file_download($filename, $output);
}
