#!/usr/bin/env php
<?php
if (php_sapi_name() === 'cli') {
    $init_modules = [];
    require realpath(__DIR__ . '/..') . '/includes/init.php';

    $return = \Artisan::call('smokeping:generate --targets --no-header --no-dns --single-process --compat');
    echo \Artisan::output();

    exit($return);
}

exit;
