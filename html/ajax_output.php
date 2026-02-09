<?php
use ObzoraNMS\Util\Debug;

session_start();
session_write_close();
if (isset($_SESSION['stage']) && $_SESSION['stage'] == 2) {
    $init_modules = ['web', 'nodb'];
    require realpath(__DIR__ . '/..') . '/includes/init.php';
} else {
    $init_modules = ['web', 'auth', 'alerts'];
    require realpath(__DIR__ . '/..') . '/includes/init.php';

    if (! Auth::check()) {
        exit('Unauthorized');
    }
}

Debug::set($_REQUEST['debug'] ?? false);
$id = basename($_REQUEST['id']);

if ($id && is_file(\App\Facades\ObzoraConfig::get('install_dir') . "/includes/html/output/$id.inc.php")) {
    require \App\Facades\ObzoraConfig::get('install_dir') . "/includes/html/output/$id.inc.php";
}
