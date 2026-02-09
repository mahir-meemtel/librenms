#!/usr/bin/env php
<?php
use App\Facades\ObzoraConfig;

$init_modules = ['nodb'];
require __DIR__ . '/includes/init.php';

if (App::runningInConsole()) {
    echo ObzoraConfig::toJson();
}
