<?php
use App\Facades\ObzoraConfig;
use ObzoraNMS\Authentication\LegacyAuth;
use ObzoraNMS\Util\Debug;
use ObzoraNMS\Util\Laravel;

global $vars, $console_color;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$install_dir = realpath(__DIR__ . '/..');
chdir($install_dir);

// composer autoload
if (! is_file($install_dir . '/vendor/autoload.php')) {
    require_once $install_dir . '/includes/common.php';
    c_echo("%RError: Missing dependencies%n, run: %B./scripts/composer_wrapper.php install --no-dev%n\n\n");
}
require_once $install_dir . '/vendor/autoload.php';

if (! function_exists('module_selected')) {
    function module_selected($module, $modules)
    {
        return in_array($module, (array) $modules);
    }
}

// function only files
require_once $install_dir . '/includes/common.php';
require_once $install_dir . '/includes/dbFacile.php';
require_once $install_dir . '/includes/syslog.php';
require_once $install_dir . '/includes/snmp.inc.php';
require_once $install_dir . '/includes/services.inc.php';
require_once $install_dir . '/includes/functions.php';
require_once $install_dir . '/includes/rewrites.php';

if (module_selected('web', $init_modules)) {
    require_once $install_dir . '/includes/html/functions.inc.php';
}

if (module_selected('discovery', $init_modules)) {
    require_once $install_dir . '/includes/discovery/functions.inc.php';
}

if (module_selected('polling', $init_modules)) {
    require_once $install_dir . '/includes/polling/functions.inc.php';
}

// Boot Laravel
if (module_selected('web', $init_modules)) {
    Laravel::bootWeb(module_selected('auth', $init_modules));
} else {
    Laravel::bootCli();
}
Debug::set($debug ?? false); // override laravel configured settings (hides legacy errors too)

if (! module_selected('nodb', $init_modules)) {
    if (! \ObzoraNMS\DB\Eloquent::isConnected()) {
        echo "Could not connect to database, check logs/obzora.log.\n";

        if (! extension_loaded('mysqlnd') || ! extension_loaded('pdo_mysql')) {
            echo "\nYour PHP is missing required mysql extension(s), please install and enable.\n";
            echo "Check the install docs for more info: https://docs.obzora.meemtel.com/Installation/\n";
        }

        exit(1);
    }
}
\ObzoraNMS\DB\Eloquent::setStrictMode(false); // disable strict mode for legacy code...

if (is_numeric(ObzoraConfig::get('php_memory_limit')) && ObzoraConfig::get('php_memory_limit') > 128) {
    ini_set('memory_limit', ObzoraConfig::get('php_memory_limit') . 'M');
}

try {
    LegacyAuth::get();
} catch (Exception $exception) {
    print_error('ERROR: no valid auth_mechanism defined!');
    echo $exception->getMessage() . PHP_EOL;
    exit;
}

if (module_selected('web', $init_modules)) {
    require $install_dir . '/includes/html/vars.inc.php';
}

$console_color = new Console_Color2();
