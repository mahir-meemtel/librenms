<?php
use ObzoraNMS\Util\Debug;

$init_modules = ['web', 'auth'];
require realpath(__DIR__ . '/..') . '/includes/init.php';

if (! Auth::check()) {
    exit('Unauthorized');
}

Debug::set(! empty($_REQUEST['debug']));

$current = $_REQUEST['current'];
settype($current, 'integer');
$rowCount = $_REQUEST['rowCount'];
settype($rowCount, 'integer');
$sort = '';
if (isset($_REQUEST['sort']) && is_array($_REQUEST['sort'])) {
    foreach ($_REQUEST['sort'] as $k => $v) {
        $k = preg_replace('/[^A-Za-z0-9_]/', '', $k); // only allow plain columns
        $v = strtolower($v) == 'desc' ? 'DESC' : 'ASC';
        $sort .= " $k $v";
    }
}

$searchPhrase = $_REQUEST['searchPhrase'];
$id = basename($_REQUEST['id']);
$response = [];

if ($id && file_exists("includes/html/table/$id.inc.php")) {
    header('Content-type: application/json');
    include_once "includes/html/table/$id.inc.php";
}
