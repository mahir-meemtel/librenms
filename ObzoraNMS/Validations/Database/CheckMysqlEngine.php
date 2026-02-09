<?php
namespace ObzoraNMS\Validations\Database;

use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\Interfaces\ValidationFixer;
use ObzoraNMS\ValidationResult;

class CheckMysqlEngine implements Validation, ValidationFixer
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        $tables = $this->findNonInnodbTables();

        if ($tables->isNotEmpty()) {
            return ValidationResult::warn(trans('validation.validations.database.CheckMysqlEngine.fail'))
                ->setFixer(__CLASS__)
                ->setList(trans('validation.validations.database.CheckMysqlEngine.tables'), $tables->all());
        }

        return ValidationResult::ok(trans('validation.validations.database.CheckMysqlEngine.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected();
    }

    /**
     * @inheritDoc
     */
    public function fix(): bool
    {
        try {
            $db = $this->databaseName();
            $tables = $this->findNonInnodbTables();

            foreach ($tables as $table) {
                DB::statement("ALTER TABLE $db.$table ENGINE=InnoDB;");
            }
        } catch (QueryException $e) {
            return false;
        }

        return true;
    }

    private function databaseName(): string
    {
        return \config('database.connections.' . \config('database.default') . '.database');
    }

    private function findNonInnodbTables(): Collection
    {
        $db = $this->databaseName();

        return DB::table('information_schema.tables')
            ->where('TABLE_SCHEMA', $db)
            ->where('ENGINE', '!=', 'InnoDB')
            ->pluck('TABLE_NAME');
    }
}
