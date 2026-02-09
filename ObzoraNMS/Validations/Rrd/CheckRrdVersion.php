<?php
namespace ObzoraNMS\Validations\Rrd;

use App\Facades\ObzoraConfig;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\Interfaces\ValidationFixer;
use ObzoraNMS\Util\Version;
use ObzoraNMS\ValidationResult;
use Storage;

class CheckRrdVersion implements Validation, ValidationFixer
{
    public function validate(): ValidationResult
    {
        // Check that rrdtool config version is what we see
        $rrd_version = Version::get()->rrdtool();
        $config_version = ObzoraConfig::get('rrdtool_version');

        if (version_compare($config_version, '1.5.5', '<')
            && version_compare($config_version, $rrd_version, '>')
        ) {
            return ValidationResult::fail(
                trans('validation.validations.rrd.CheckRrdVersion.fail', ['config_version' => $config_version, 'installed_version' => $rrd_version]),
                trans('validation.validations.rrd.CheckRrdVersion.fix', ['version' => $config_version])
            )->setFixer(__CLASS__, is_writable(base_path('config.php')));
        }

        return ValidationResult::ok(trans('validation.validations.rrd.CheckRrdVersion.ok'));
    }

    public function enabled(): bool
    {
        return ObzoraConfig::has('rrdtool_version');
    }

    public function fix(): bool
    {
        try {
            $contents = Storage::disk('base')->get('config.php');

            $lines = array_filter(explode("\n", $contents), function ($line) {
                return ! Str::contains($line, ['$config[\'rrdtool_version\']', '$config["rrdtool_version"]']);
            });

            return Storage::disk('base')->put('config.php', implode("\n", $lines));
        } catch (FileNotFoundException $e) {
            return false;
        }
    }
}
