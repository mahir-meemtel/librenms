<?php
namespace App\Http\Controllers\Install;

use ObzoraNMS\Interfaces\InstallerStep;
use ObzoraNMS\Validations\Php;

class ChecksController extends InstallationController implements InstallerStep
{
    const MODULES = ['pdo_mysql', 'mysqlnd', 'gd'];
    protected $step = 'checks';

    public function index()
    {
        $this->initInstallStep();

        if ($this->complete()) {
            $this->markStepComplete();
        }

        preg_match('/\d+\.\d+\.\d+/', PHP_VERSION, $matches);
        $version = $matches[0] ?? PHP_VERSION;

        return view('install.checks', $this->formatData([
            'php_version' => $version,
            'php_required' => Php::PHP_MIN_VERSION,
            'php_ok' => $this->checkPhpVersion(),
            'modules' => $this->moduleResults(),
        ]));
    }

    private function moduleResults()
    {
        $results = [];

        foreach (self::MODULES as $module) {
            $status = extension_loaded($module);
            $results[] = [
                'name' => str_replace('install.checks.php_module.', '', trans('install.checks.php_module.' . $module)),
                'status' => $status,
            ];
        }

        return $results;
    }

    private function checkPhpVersion()
    {
        return version_compare(PHP_VERSION, Php::PHP_MIN_VERSION, '>=');
    }

    public function complete(): bool
    {
        if ($this->stepCompleted('checks')) {
            return true;
        }

        if (! $this->checkPhpVersion()) {
            return false;
        }

        foreach (self::MODULES as $module) {
            if (! extension_loaded($module)) {
                return false;
            }
        }

        return true;
    }

    public function enabled(): bool
    {
        return true;
    }

    public function icon(): string
    {
        return 'fa-solid fa-list-ul fa-flip-horizontal';
    }
}
