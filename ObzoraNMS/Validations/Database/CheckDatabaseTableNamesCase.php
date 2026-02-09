<?php
namespace ObzoraNMS\Validations\Database;

use Illuminate\Support\Facades\DB;
use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\ValidationResult;

class CheckDatabaseTableNamesCase implements Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        // Test for lower case table name support
        $lc_mode = DB::selectOne('SELECT @@global.lower_case_table_names as mode')->mode;
        if ($lc_mode != 0) {
            ValidationResult::fail(
                trans('validation.validations.database.CheckDatabaseTableNamesCase.fail'),
                trans('validation.validations.database.CheckDatabaseTableNamesCase.fix')
            );
        }

        return ValidationResult::ok(trans('validation.validations.database.CheckDatabaseTableNamesCase.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected();
    }
}
