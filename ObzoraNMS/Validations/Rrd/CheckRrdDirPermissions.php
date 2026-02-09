<?php
namespace ObzoraNMS\Validations\Rrd;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\ValidationResult;

class CheckRrdDirPermissions implements Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        $rrd_dir = ObzoraConfig::get('rrd_dir');

        $dir_stat = stat($rrd_dir);
        if ($dir_stat[4] == 0 || $dir_stat[5] == 0) {
            return ValidationResult::warn(trans('validation.validations.rrd.CheckRrdDirPermissions.fail_root'),
                sprintf('chown %s:%s %s', ObzoraConfig::get('user'), ObzoraConfig::get('group'), $rrd_dir)
            );
        }

        if (substr(sprintf('%o', fileperms($rrd_dir)), -3) != 775) {
            return ValidationResult::warn(trans('validation.validations.rrd.CheckRrdDirPermissions.fail_mode'), "chmod 775 $rrd_dir");
        }

        return ValidationResult::ok(trans('validation.validations.rrd.CheckRrdDirPermissions.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return ! ObzoraConfig::get('rrdcached');
    }
}
