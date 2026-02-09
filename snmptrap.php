#!/usr/bin/env php
<?php
use ObzoraNMS\Util\Debug;

$init_modules = [];
require __DIR__ . '/includes/init.php';

$options = getopt('d::');

if (Debug::set(isset($options['d']))) {
    echo "DEBUG!\n";
}

$text = stream_get_contents(STDIN);

// create handle and send it this trap
\ObzoraNMS\Snmptrap\Dispatcher::handle(new \ObzoraNMS\Snmptrap\Trap($text));
