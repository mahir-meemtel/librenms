<?php
use ObzoraNMS\Util\Debug;

$init_modules = ['web', 'auth'];
require realpath(__DIR__ . '/..') . '/includes/init.php';

if (! Auth::check()) {
    exit('Unauthorized');
}

Debug::set($_REQUEST['debug'] ?? false);

$type = basename($_REQUEST['type']);

if ($type && file_exists("includes/html/list/$type.inc.php")) {
    header('Content-type: application/json');

    [$results, $more] = include "includes/html/list/$type.inc.php";

    exit(json_encode([
        'results' => $results,
        'pagination' => ['more' => $more],
    ]));
}
