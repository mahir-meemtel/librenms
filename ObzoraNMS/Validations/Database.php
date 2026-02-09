<?php
namespace ObzoraNMS\Validations;

use ObzoraNMS\Validations\Database\CheckDatabaseServerVersion;
use ObzoraNMS\Validations\Database\CheckDatabaseTableNamesCase;
use ObzoraNMS\Validations\Database\CheckMysqlEngine;
use ObzoraNMS\Validations\Database\CheckSqlServerTime;
use ObzoraNMS\Validator;

class Database extends BaseValidation
{
    public const MYSQL_MIN_VERSION = '5.7.7';
    public const MYSQL_MIN_VERSION_DATE = 'March, 2021';
    public const MYSQL_RECOMMENDED_VERSION = '8.0';

    public const MARIADB_MIN_VERSION = '10.2.2';
    public const MARIADB_MIN_VERSION_DATE = 'March, 2021';
    public const MARIADB_RECOMMENDED_VERSION = '10.5';

    protected $directory = 'Database';
    protected $name = 'database';

    /**
     * Tests used by the installer to validate that SQL server doesn't have any known issues (before migrations)
     */
    public function validateSystem(Validator $validator): void
    {
        $validator->result((new CheckDatabaseServerVersion)->validate(), $this->name);
        $validator->result((new CheckMysqlEngine)->validate(), $this->name);
        $validator->result((new CheckSqlServerTime)->validate(), $this->name);
        $validator->result((new CheckDatabaseTableNamesCase)->validate(), $this->name);
    }
}
