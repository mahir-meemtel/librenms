<?php
use ObzoraNMS\RRD\RrdDefinition;
use ObzoraNMS\Util\Number;

$data = '';
$name = 'rrdcached';

if ($agent_data['app'][$name]) {
    $data = $agent_data['app'][$name];
} else {
    d_echo("\nNo Agent Data. Attempting to connect directly to the rrdcached server " . $device['hostname'] . ":42217\n");

    $sock = fsockopen($device['hostname'], 42217, $errno, $errstr, 5);

    if (! $sock) {
        d_echo("\nNo Socket to rrdcached server " . $device['hostname'] . ":42217 try to get rrdcached from SNMP\n");
        $oid = '.1.3.6.1.4.1.8072.1.3.2.3.1.2.9.114.114.100.99.97.99.104.101.100';
        $result = snmp_get($device, $oid, '-Oqv');
        $data = trim($result, '"');
        $data = str_replace("<<<rrdcached>>>\n", '', $data);
    }
    if (strlen($data) < 100) {
        $socket = \App\Facades\ObzoraConfig::get('rrdcached');
        if (substr($socket, 0, 6) == 'unix:/') {
            $socket_file = substr($socket, 5);
            if (file_exists($socket_file)) {
                $sock = fsockopen('unix://' . $socket_file);
            }
        }
        d_echo("\nNo SnmpData " . $device['hostname'] . ' fallback to local rrdcached unix://' . $socket_file . "\n");
    }
    if ($sock) {
        fwrite($sock, "STATS\n");
        $max = -1;
        $count = 0;
        while ($max == -1 || $count < $max) {
            $data .= fgets($sock, 128);
            if ($max == -1) {
                $tmp_max = explode(' ', $data);
                $max = Number::cast($tmp_max[0]) + 1;
            }
            $count++;
        }
        fclose($sock);
    } elseif (strlen($data) < 100) {
        d_echo("ERROR: $errno - $errstr\n");
    }
}

$rrd_def = RrdDefinition::make()
    ->addDataset('queue_length', 'GAUGE', 0)
    ->addDataset('updates_received', 'COUNTER', 0)
    ->addDataset('flushes_received', 'COUNTER', 0)
    ->addDataset('updates_written', 'COUNTER', 0)
    ->addDataset('data_sets_written', 'COUNTER', 0)
    ->addDataset('tree_nodes_number', 'GAUGE', 0)
    ->addDataset('tree_depth', 'GAUGE', 0)
    ->addDataset('journal_bytes', 'COUNTER', 0)
    ->addDataset('journal_rotate', 'COUNTER', 0);

$fields = [];
foreach (explode("\n", $data) as $line) {
    $split = explode(': ', $line);
    if (count($split) == 2) {
        $ds = strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($split[0])));
        $fields[$ds] = $split[1];
    }
}

$tags = [
    'name' => $name,
    'app_id' => $app->app_id,
    'rrd_name' => ['app', $name, $app->app_id],
    'rrd_def' => $rrd_def,
];
app('Datastore')->put($device, 'app', $tags, $fields);
update_application($app, $data, $fields);

unset($data, $rrd_def, $fields, $tags);
