<?php
namespace ObzoraNMS\Validations;

use ObzoraNMS\Util\EnvHelper;
use ObzoraNMS\Util\Git;
use ObzoraNMS\ValidationResult;
use ObzoraNMS\Validator;

class Dependencies extends BaseValidation
{
    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        if (EnvHelper::obzoraDocker()) {
            $validator->ok('Installed from the official Docker image; no Composer required');

            return;
        }

        // if git is not installed, do not assume composer is either
        if (! Git::make()->repoPresent()) {
            $validator->ok('Installed from package; no Composer required');

            return;
        }

        $composer_output = trim(shell_exec("'" . $validator->getBaseDir() . "/scripts/composer_wrapper.php' --version"));
        $found = preg_match(
            '/Composer.*(\d+\.\d+\.\d+(-RC\d*|-beta\d?|-alpha\d+)?)/',
            $composer_output,
            $matches
        );

        if (! $found) {
            $validator->fail('No composer available, please install composer', 'https://getcomposer.org/');

            return;
        } else {
            $validator->ok('Composer Version: ' . $matches[1]);
        }

        $dep_check = shell_exec("'" . $validator->getBaseDir() . "/scripts/composer_wrapper.php' install --no-dev --dry-run");
        preg_match_all('/Installing ([^ ]+\/[^ ]+) \(/', $dep_check, $dep_missing);
        if (! empty($dep_missing[0])) {
            $result = ValidationResult::fail('Missing dependencies!', $validator->getBaseDir() . '/scripts/composer_wrapper.php install --no-dev');
            $result->setList('Dependencies', $dep_missing[1]);
            $validator->result($result);
        }
        preg_match_all('/Updating ([^ ]+\/[^ ]+) \(/', $dep_check, $dep_outdated);
        if (! empty($dep_outdated[0])) {
            $result = ValidationResult::fail('Outdated dependencies', $validator->getBaseDir() . '/scripts/composer_wrapper.php install --no-dev');
            $result->setList('Dependencies', $dep_outdated[1]);
        }

        if (empty($dep_missing[0]) && empty($dep_outdated[0])) {
            $validator->ok('Dependencies up-to-date.');
        }
    }
}
