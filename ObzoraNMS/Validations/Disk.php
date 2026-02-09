<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Validator;

class Disk extends BaseValidation
{
    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        // Disk space and permission checks
        $temp_dir = ObzoraConfig::get('temp_dir');
        if (substr(sprintf('%o', fileperms($temp_dir)), -3) != 777) {
            $validator->warn("Your tmp directory ($temp_dir) " .
                "is not set to 777 so graphs most likely won't be generated");
        }

        $rrd_dir = ObzoraConfig::get('rrd_dir');
        $space_check = (disk_free_space($rrd_dir) / 1024 / 1024);
        if ($space_check < 512 && $space_check > 1) {
            $validator->warn("Disk space where $rrd_dir is located is less than 512Mb");
        }

        if ($space_check < 1) {
            $validator->fail("Disk space where $rrd_dir is located is empty!!!");
        }
    }
}
