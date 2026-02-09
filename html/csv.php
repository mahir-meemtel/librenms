<?php
use App\Facades\ObzoraConfig;
use ObzoraNMS\Util\Debug;

$init_modules = ['web', 'auth'];
require realpath(__DIR__ . '/..') . '/includes/init.php';

if (! Auth::check()) {
    exit('Unauthorized');
}

Debug::set(strpos($_SERVER['PATH_INFO'], 'debug'));

$report = basename($vars['report']);
if ($report && file_exists(ObzoraConfig::get('install_dir') . "/includes/html/reports/$report.csv.inc.php")) {
    if (! Debug::isEnabled()) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $report . '-' . date('Ymd') . '.csv"');
    }

    $csv = [];
    require ObzoraConfig::get('install_dir') . "/includes/html/reports/$report.csv.inc.php";
    foreach ($csv as $line) {
        echo implode(',', $line) . "\n";
    }
} else {
    echo "Report not found.\n";
}
