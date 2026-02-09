#!/usr/bin/env php
<?php
$init_modules = ['polling', 'alerts', 'laravel'];
require __DIR__ . '/includes/init.php';

$options = getopt('h:rfpdvm:q');

c_echo('%RWarning: poller.php is deprecated!%n Use %9lnms device:poll%n instead.' . PHP_EOL . PHP_EOL);

$scheduler = \App\Facades\ObzoraConfig::get('schedule_type.poller');
if ($scheduler != 'legacy' && $scheduler != 'cron') {
    exit(0); // message above is sufficient
}

if (empty($options['h'])) {
    echo "-h <device id> | <device hostname wildcard>  Poll single device\n";
    echo "-h odd             Poll odd numbered devices\n";
    echo "-h even            Poll even numbered devices\n";
    echo "-h all             Poll all devices\n\n";
    echo "Debugging and testing options:\n";
    echo "-r                 Do not create or update RRDs\n";
    echo "-f                 Do not insert data into InfluxDB\n";
    echo "-p                 Do not insert data into Prometheus\n";
    echo "-d                 Enable debugging output\n";
    echo "-v                 Enable verbose debugging output\n";
    echo "-m                 Specify module(s) to be run. Comma separate modules, submodules may be added with /\n";
    echo "-q                 Quiet, minimal output /\n";
    echo "\n";
    echo "No polling type specified!\n";
    exit;
}

$arguments = [
    'device spec' => $options['h'],
    '--verbose' => isset($options['v']) ? 3 : (isset($options['d']) ? 2 : 1),
];

if (isset($options['m'])) {
    $arguments['--modules'] = $options['m'];
}

if (isset($options['q'])) {
    $arguments['--quiet'] = true;
}

if (isset($options['r']) || isset($options['f']) || isset($options['p'])) {
    $arguments['--no-data'] = true;
}

$return = Artisan::call('device:poll', $arguments);

exit($return);
