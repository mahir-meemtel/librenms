<?php
namespace ObzoraNMS\Validations\Database;

use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\Util\Version;
use ObzoraNMS\ValidationResult;
use ObzoraNMS\Validations\Database;

class CheckDatabaseServerVersion implements Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        $version = Version::get()->databaseServer();
        [$name, $version] = explode(' ', $version, 2);
        [$version] = explode('-', $version, 2);

        switch ($name) {
            case 'MariaDB':
                if (version_compare($version, Database::MARIADB_MIN_VERSION, '<=')) {
                    return ValidationResult::fail(
                        trans('validation.validations.database.CheckDatabaseServerVersion.fail', ['server' => 'MariaDB', 'min' => Database::MARIADB_MIN_VERSION, 'date' => Database::MARIADB_MIN_VERSION_DATE]),
                        trans('validation.validations.database.CheckDatabaseServerVersion.fix', ['server' => 'MariaDB', 'suggested' => Database::MARIADB_RECOMMENDED_VERSION]),
                    );
                }
                break;
            case 'MySQL':
                if (version_compare($version, Database::MYSQL_MIN_VERSION, '<=')) {
                    return ValidationResult::fail(
                        trans('validation.validations.database.CheckDatabaseServerVersion.fail', ['server' => 'MySQL', 'min' => Database::MYSQL_MIN_VERSION, 'date' => Database::MYSQL_MIN_VERSION_DATE]),
                        trans('validation.validations.database.CheckDatabaseServerVersion.fix', ['server' => 'MySQL', 'suggested' => Database::MYSQL_RECOMMENDED_VERSION]),
                    );
                }
                break;
        }

        return ValidationResult::ok(trans('validation.validations.database.CheckDatabaseServerVersion.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected();
    }
}
