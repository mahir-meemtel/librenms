<?php
namespace ObzoraNMS;

use Composer\Script\Event;
use ObzoraNMS\Exceptions\FileWriteFailedException;
use ObzoraNMS\Util\EnvHelper;
use Minishlink\WebPush\VAPID;

class ComposerHelper
{
    public static function postRootPackageInstall(Event $event)
    {
        if (! file_exists('.env')) {
            self::setPermissions();
            self::populateEnv();
        }
    }

    public static function postInstall(Event $event)
    {
        if (! file_exists('.env')) {
            self::setPermissions();
        }

        self::populateEnv();
    }

    public static function preUpdate(Event $event)
    {
        if (! getenv('FORCE')) {
            echo "Running composer update is not advisable.  Please run composer install to update instead.\n";
            echo "If know what you are doing and want to write a new composer.lock file set FORCE=1.\n";
            echo "If you don't know what to do, run: composer install\n";
            exit(1);
        }
    }

    public static function preInstall(Event $event)
    {
    }

    public static function addPlugin(string $package, ?string $version = null): int
    {
        $package = escapeshellarg($package . ($version ? ":$version" : null));

        $cmds = [
            'COMPOSER=composer.plugins.json ' . PHP_BINARY . " ./scripts/composer_wrapper.php require --no-update $package",
        ];

        return self::exec($cmds);
    }

    public static function addPackage(string $package, ?string $version = null): int
    {
        $package = escapeshellarg($package . ($version ? ":$version" : null));

        $cmds = [
            'FORCE=1 ' . PHP_BINARY . " ./scripts/composer_wrapper.php require --update-no-dev $package",
        ];

        return self::exec($cmds);
    }

    public static function removePlugin(string $package): int
    {
        $package = escapeshellarg($package);

        $cmds = [
            'COMPOSER=composer.plugins.json ' . PHP_BINARY . " ./scripts/composer_wrapper.php remove --no-update $package",
        ];

        return self::exec($cmds);
    }

    public static function removePackage(string $package): int
    {
        $package = escapeshellarg($package);

        $cmds = [
            'FORCE=1 ' . PHP_BINARY . " ./scripts/composer_wrapper.php remove --update-no-dev $package",
        ];

        return self::exec($cmds);
    }

    public static function getPlugins(): array
    {
        $plugins = is_file('composer.plugins.json') ?
            json_decode(file_get_contents('composer.plugins.json'), true) : [];

        return $plugins['require'] ?? [];
    }

    /**
     * Initially populate .env file
     */
    private static function populateEnv()
    {
        $config = [
            'db_host' => '',
            'db_port' => '',
            'db_name' => '',
            'db_user' => '',
            'db_pass' => '',
            'db_socket' => '',
            'base_url' => '',
            'user' => '',
            'group' => '',
        ];

        if (file_exists('config.php')) {
            /** @phpstan-ignore include.fileNotFound */
            @include 'config.php';
        }

        try {
            EnvHelper::init();
            $vapid = VAPID::createVapidKeys();

            EnvHelper::writeEnv([
                'NODE_ID' => uniqid(),
                'DB_HOST' => $config['db_host'],
                'DB_PORT' => $config['db_port'],
                'DB_USERNAME' => $config['db_user'],
                'DB_PASSWORD' => $config['db_pass'],
                'DB_DATABASE' => $config['db_name'],
                'DB_SOCKET' => $config['db_socket'],
                'APP_URL' => $config['base_url'],
                'OBZORA_USER' => $config['user'],
                'OBZORA_GROUP' => $config['group'],
                'VAPID_PUBLIC_KEY' => $vapid['publicKey'],
                'VAPID_PRIVATE_KEY' => $vapid['privateKey'],
            ]);
        } catch (FileWriteFailedException $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }

    private static function setPermissions()
    {
        $permissions_cmds = [
            'setfacl -R -m g::rwx rrd/ logs/ storage/ bootstrap/cache/',
            'setfacl -d -m g::rwx rrd/ logs/ storage/ bootstrap/cache/',
        ];

        self::exec($permissions_cmds);
    }

    /**
     * Run a command or array of commands and echo the command and output
     *
     * @param  string|array  $cmds
     */
    private static function exec($cmds): int
    {
        $cmd = "set -v\n" . implode(PHP_EOL, (array) $cmds);
        passthru($cmd, $result_code);

        return $result_code;
    }
}
