#!/usr/bin/env php
<?php
use App\Models\Port;
use Illuminate\Database\Eloquent\ModelNotFoundException;

chdir(dirname($argv[0]));

$init_modules = [];
require realpath(__DIR__ . '/..') . '/includes/init.php';

$opt = getopt('p:f:');

// Single Port-id given on cmdline?
$port_id = null;
if ($opt['p']) {
    $port_id = $opt['p'];
}

// File with port-ids given on cmdline?
$port_id_file = null;
if ($opt['f']) {
    $port_id_file = $opt['f'];
}

if (! $port_id && ! $port_id_file || ($port_id && $port_id_file)) {
    echo $console_color->convert(\App\Facades\ObzoraConfig::get('project_name') . ' Port purge tool
    -p <port_id>  Purge single port by it\'s port-id
    -f <file>     Purge a list of ports, read port-ids from <file>, one on each line.
                  A filename of - means reading from STDIN.
');
}

// Purge single port
if ($port_id) {
    try {
        Port::findOrFail($port_id)->delete();
    } catch (ModelNotFoundException $e) {
        echo "Port ID $port_id not found!\n";
    }
}

// Delete multiple ports
if ($port_id_file) {
    $fh = null;
    if ($port_id_file == '-') {
        $fh = STDIN;
    } else {
        $fh = fopen($port_id_file, 'r');
        if (! $fh) {
            echo 'Failed to open port-id list "' . $port_id_file . "\": \n";
            exit(1);
        }
    }

    while ($port_id = trim(fgets($fh))) {
        try {
            Port::findOrFail($port_id)->delete();
        } catch (ModelNotFoundException $e) {
            echo "Port ID $port_id not found!\n";
        }
    }

    if ($fh != STDIN) {
        fclose($fh);
    }
}
