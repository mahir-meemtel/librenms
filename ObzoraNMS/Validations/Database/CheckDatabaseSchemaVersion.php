<?php
namespace ObzoraNMS\Validations\Database;

use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\DB\Schema;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\Interfaces\ValidationFixer;
use ObzoraNMS\ValidationResult;

class CheckDatabaseSchemaVersion implements Validation, ValidationFixer
{
    /** @var bool|null */
    private static $current = null;

    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        self::$current = false;

        if (! Schema::isCurrent()) {
            return ValidationResult::fail(trans('validation.validations.database.CheckSchemaVersion.fail_outdated'), './lnms migrate')
                ->setFixer(__CLASS__);
        }

        $migrations = Schema::getUnexpectedMigrations();
        if ($migrations->isNotEmpty()) {
            return ValidationResult::warn(trans('validation.validations.database.CheckSchemaVersion.warn_extra_migrations', ['migrations' => $migrations->implode(', ')]));
        }

        self::$current = true;

        return ValidationResult::ok(trans('validation.validations.database.CheckSchemaVersion.ok'));
    }

    public static function isCurrent(): bool
    {
        if (self::$current === null) {
            (new static)->validate();
        }

        return self::$current;
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected();
    }

    public function fix(): bool
    {
        return \Artisan::call('migrate', ['--force' => true, '--isolated' => true]) === 0;
    }
}
