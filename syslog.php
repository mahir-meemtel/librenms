#!/usr/bin/env php
<?php
$init_modules = [];
require __DIR__ . '/includes/init.php';

$keys = ['host', 'facility', 'priority', 'level', 'tag', 'timestamp', 'msg', 'program'];

$s = fopen('php://stdin', 'r');
while ($line = fgets($s)) {
    //logfile($line);

    $fields = explode('||', trim($line));
    if (count($fields) === 8) {
        process_syslog(array_combine($keys, $fields), 1);
    }

    unset($line, $fields);
}
