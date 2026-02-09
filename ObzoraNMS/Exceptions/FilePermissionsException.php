<?php
namespace ObzoraNMS\Exceptions;

use App\Facades\ObzoraConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Exceptions\UpgradeableException;
use Throwable;

class FilePermissionsException extends \Exception implements UpgradeableException
{
    /**
     * Try to convert the given Exception to a FilePermissionsException
     */
    public static function upgrade(Throwable $exception): ?static
    {
        // cannot write to storage directory
        if ($exception instanceof \ErrorException &&
            Str::startsWith($exception->getMessage(), 'file_put_contents(') &&
            Str::contains($exception->getMessage(), '/storage/')) {
            return new static();
        }

        // cannot write to bootstrap directory
        if ($exception instanceof \Exception && $exception->getMessage() == 'The bootstrap/cache directory must be present and writable.') {
            return new static();
        }

        // monolog cannot init log file
        if ($exception instanceof \UnexpectedValueException && Str::contains($exception->getFile(), 'Monolog/Handler/StreamHandler.php')) {
            return new static();
        }

        return null;
    }

    /**
     * Render the exception into an HTTP or JSON response.
     */
    public function render(Request $request): Response
    {
        $log_file = config('app.log') ?: ObzoraConfig::get('log_file', base_path('logs/obzora.log'));
        $commands = $this->generateCommands($log_file);

        // use pre-compiled template because we probably can't compile it.
        $template = file_get_contents(base_path('resources/views/errors/static/file_permissions.html'));
        $content = str_replace('!!!!CONTENT!!!!', '<p>' . implode('</p><p>', $commands) . '</p>', $template);
        $content = str_replace('!!!!LOG_FILE!!!!', $log_file, $content);

        return response($content);
    }

    /**
     * @param  string  $log_file
     * @return array
     */
    private function generateCommands($log_file): array
    {
        $user = config('obzora.user');
        $group = config('obzora.group');
        $install_dir = base_path();
        $commands = [];
        $dirs = [
            base_path('bootstrap/cache'),
            base_path('storage'),
            ObzoraConfig::get('log_dir', base_path('logs')),
            ObzoraConfig::get('rrd_dir', base_path('rrd')),
        ];

        // check if folders are missing
        $mkdirs = [
            base_path('bootstrap/cache'),
            base_path('storage/framework/sessions'),
            base_path('storage/framework/views'),
            base_path('storage/framework/cache'),
            ObzoraConfig::get('log_dir', base_path('logs')),
            ObzoraConfig::get('rrd_dir', base_path('rrd')),
        ];

        $mk_dirs = array_filter($mkdirs, function ($file) {
            return ! file_exists($file);
        });

        if (! empty($mk_dirs)) {
            $commands[] = 'sudo mkdir -p ' . implode(' ', $mk_dirs);
        }

        // always print chwon/setfacl/chmod commands
        $commands[] = "sudo chown -R $user:$group '$install_dir'";
        $commands[] = 'sudo setfacl -d -m g::rwx ' . implode(' ', $dirs);
        $commands[] = 'sudo chmod -R ug=rwX ' . implode(' ', $dirs);

        // check if webserver is in the obzora group
        $current_groups = explode(' ', trim(exec('groups')));
        if (! in_array($group, $current_groups)) {
            $current_user = trim(exec('whoami'));
            $commands[] = "usermod -a -G $group $current_user";
        }

        // check for invalid log setting
        if (! is_file($log_file) || ! is_writable($log_file)) {
            // override for proper error output
            $dirs = [$log_file];
            $install_dir = $log_file;
            $commands = [
                '<h3>Cannot write to log file: &quot;' . $log_file . '&quot;</h3>',
                'Make sure it exists and is writable, or change your LOG_DIR setting.',
            ];
        }

        // selinux:
        $commands[] = '<h4>If using SELinux you may also need:</h4>';
        foreach ($dirs as $dir) {
            $commands[] = "semanage fcontext -a -t httpd_sys_rw_content_t '$dir(/.*)?'";
        }
        $commands[] = "restorecon -RFv $install_dir";

        return $commands;
    }
}
