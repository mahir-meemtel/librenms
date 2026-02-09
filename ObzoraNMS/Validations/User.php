<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use ObzoraNMS\Util\EnvHelper;
use ObzoraNMS\Util\Git;
use ObzoraNMS\ValidationResult;
use ObzoraNMS\Validator;

class User extends BaseValidation
{
    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        // Check we are running this as the root user
        $username = $validator->getUsername();
        $lnms_username = \config('obzora.user');
        $lnms_groupname = \config('obzora.group');

        if (! ($username === 'root' || $username === $lnms_username)) {
            if (App::runningInConsole()) {
                $validator->fail("You need to run this script as '$lnms_username' or root");
            } elseif (function_exists('posix_getgrnam')) {
                $lnms_group = posix_getgrnam($lnms_groupname);

                if ($lnms_group === false) {
                    $validator->fail(
                        "The group '$lnms_groupname' does not exist",
                        "groupadd $lnms_groupname"
                    );
                } elseif (! in_array($username, $lnms_group['members'])) {
                    $validator->fail(
                        "Your web server or php-fpm is not running as user '$lnms_username' or in the group '$lnms_groupname'",
                        "usermod -a -G $lnms_groupname $username"
                    );
                }
            }
        }

        // skip if docker image
        if (EnvHelper::obzoraDocker()) {
            return;
        }

        // if no git, then we probably have different permissions by design
        if (! Git::make()->repoPresent()) {
            return;
        }

        // Let's test the user configured if we have it
        if ($lnms_username) {
            $dir = ObzoraConfig::get('install_dir');
            $log_dir = ObzoraConfig::get('log_dir', "$dir/logs");
            $rrd_dir = ObzoraConfig::get('rrd_dir', "$dir/rrd");

            // generic fix
            $fix = [
                "sudo chown -R $lnms_username:$lnms_groupname $dir",
                "sudo setfacl -d -m g::rwx $rrd_dir $log_dir $dir/bootstrap/cache/ $dir/storage/",
                "sudo chmod -R ug=rwX $rrd_dir $log_dir $dir/bootstrap/cache/ $dir/storage/",
            ];

            if (! ObzoraConfig::get('installed_from_package')) {
                $find_output = shell_exec("find $dir \! -user $lnms_username -o \! -group $lnms_groupname 2> /dev/null");
                $find_result = rtrim($find_output ?: '');
                if (! empty($find_result)) {
                    // Ignore files created by the webserver
                    $ignore_files = [
                        "$log_dir/error_log",
                        "$log_dir/access_log",
                        "$dir/bootstrap/cache/",
                        "$dir/storage/framework/cache/",
                        "$dir/storage/framework/sessions/",
                        "$dir/storage/framework/views/",
                        "$dir/storage/debugbar/",
                        "$dir/.pki/", // ignore files/folders created by setting the obzora home directory to the install directory
                    ];

                    $files = array_filter(explode(PHP_EOL, $find_result), function ($file) use ($ignore_files) {
                        if (Str::startsWith($file, $ignore_files)) {
                            return false;
                        }

                        return true;
                    });

                    if (! empty($files)) {
                        $result = ValidationResult::fail(
                            "We have found some files that are owned by a different user than '$lnms_username', this " .
                            'will stop you updating automatically and / or rrd files being updated causing graphs to fail.'
                        )
                            ->setFix($fix)
                            ->setList('Files', $files);

                        $validator->result($result);

                        return;
                    }
                }
            }
        } else {
            $validator->warn("You don't have OBZORA_USER set, this most likely needs to be set to 'obzora'");
        }
    }
}
