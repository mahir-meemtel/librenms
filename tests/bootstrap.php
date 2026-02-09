<?php
$install_dir = realpath(__DIR__ . '/..');

$init_modules = ['web', 'discovery', 'polling', 'nodb'];

require $install_dir . '/includes/init.php';
chdir($install_dir);

ini_set('display_errors', '1');

if (getenv('DBTEST')) {
    // create testing table if needed
    $db_config = \config('database.connections.testing');
    $connection = new PDO("mysql:host={$db_config['host']};port={$db_config['port']}", $db_config['username'], $db_config['password']);
    $result = $connection->query("CREATE DATABASE IF NOT EXISTS {$db_config['database']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if ($connection->errorCode() == '42000') {
        echo implode(' ', $connection->errorInfo()) . PHP_EOL;
        echo "Either create database {$db_config['database']} or populate DB_TEST_USERNAME and DB_TEST_PASSWORD in your .env with credentials that can" . PHP_EOL;
        exit(1);
    }
    unset($connection); // close connection

    // migrate testing database to make sure it is up-to-date
    Artisan::call('migrate', ['--seed' => true, '--env' => 'testing', '--database' => 'testing']);
    Artisan::output();
}

ObzoraConfig::invalidateAndReload();

Illuminate\Foundation\Bootstrap\HandleExceptions::flushState(); // Reset Laravels error handler

app()->terminate(); // destroy the bootstrap Laravel application
