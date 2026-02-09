<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use Illuminate\Support\Arr;
use ObzoraNMS\Validator;

class System extends BaseValidation
{
    protected static $RUN_BY_DEFAULT = true;

    /**
     * @inheritdoc
     */
    public function validate(Validator $validator): void
    {
        $install_dir = $validator->getBaseDir();

        $lnms = str_replace('lnms:', '', rtrim(`whereis -b lnms 2>/dev/null`));
        $path = rtrim(`echo "\$PATH"`);

        // if couldn't find lnms and we have PATH
        if (empty($lnms) && ! empty($path)) {
            $paths = explode(':', $path);
            $bin = Arr::first(array_intersect([
                '/usr/local/bin',
                '/usr/bin',
                '/bin',
            ], $paths), null, Arr::last($paths));

            $validator->warn('Global lnms shortcut not installed. lnms command must be run with full path', "sudo ln -s $install_dir/lnms $bin/lnms");
        }

        $bash_completion_dir = '/etc/bash_completion.d/';
        $completion_file = 'lnms-completion.bash';
        if (is_dir($bash_completion_dir) && ! file_exists("$bash_completion_dir$completion_file")) {
            $validator->warn('Bash completion not installed. lnms command tab completion unavailable.', "sudo cp $install_dir/misc/lnms-completion.bash $bash_completion_dir");
        }

        $rotation_file = '/etc/logrotate.d/obzora';
        if (! file_exists($rotation_file) && ! ObzoraConfig::get('installed_from_package')) {
            $validator->warn('Log rotation not enabled, could cause disk space issues', "sudo cp $install_dir/misc/obzora.logrotate $rotation_file");
        }
    }
}
