<?php
namespace ObzoraNMS\Validations\Database;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\ValidationResult;

class CheckSqlServerTime implements Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        $raw_time = Eloquent::DB()->selectOne('SELECT NOW() as time')->time;
        $db_time = new Carbon($raw_time);
        $php_time = Carbon::now();

        $diff = $db_time->diffAsCarbonInterval($php_time);

        if ($diff->compare(CarbonInterval::minute(1)) > 0) {
            return ValidationResult::fail(trans('validation.validations.database.CheckSqlServerTime.fail', [
                'mysql_time' => $db_time->toDateTimeString(),
                'php_time' => $php_time->toDateTimeString(),
            ]));
        }

        return ValidationResult::ok(trans('validation.validations.database.CheckSqlServerTime.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected();
    }
}
