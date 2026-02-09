<?php
if (! Auth::user()->hasGlobalAdmin()) {
    echo 'Insufficient Privileges';
    exit;
}

$hostname = escapeshellcmd($_REQUEST['hostname']);
$type = $_REQUEST['type'];

switch ($type) {
    case 'poller':
        $cmd = ['php', \App\Facades\ObzoraConfig::get('install_dir') . '/lnms', 'device:poll', $hostname, '--no-data', '-vv'];
        $filename = "poller-$hostname.txt";
        break;
    case 'snmpwalk':
        $device = device_by_name($hostname);

        $cmd = gen_snmpwalk_cmd($device, '.', '-OUneb');

        $filename = $device['os'] . '-' . $device['hostname'] . '.snmpwalk';
        break;
    case 'discovery':
        $cmd = ['php', \App\Facades\ObzoraConfig::get('install_dir') . '/discovery.php', '-h', $hostname, '-d'];
        $filename = "discovery-$hostname.txt";
        break;
    default:
        echo 'You must specify a valid type';
        exit;
}

// ---- Output ----
$proc = new \Symfony\Component\Process\Process($cmd);
$proc->setTimeout(Config::get('snmp.exec_timeout', 1200));

if ($_GET['format'] == 'text') {
    header('Content-type: text/plain');
    header('X-Accel-Buffering: no');

    $proc->run(function ($type, $buffer) {
        echo preg_replace('/\033\[[\d;]+m/', '', $buffer) . PHP_EOL;
        ob_flush();
        flush(); // you have to flush buffer
    });
} elseif ($_GET['format'] == 'download') {
    $proc->run();
    $output = $proc->getOutput();

    $output = preg_replace('/\033\[[\d;]+m/', '', $output);

    file_download($filename, $output);
}
