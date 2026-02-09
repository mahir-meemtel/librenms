<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Util\Version;
use ObzoraNMS\Validator;
use Symfony\Component\Process\Process;

class Python extends BaseValidation
{
    const PYTHON_MIN_VERSION = '3.4.0';

    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        $version = Version::get()->python();

        if (empty($version)) {
            $validator->fail('python3 not found', 'Install Python 3 for your system.');

            return; // no need to check anything else
        }

        $this->checkVersion($validator, $version);
        $this->checkPipVersion($validator, $version);
        $this->checkExtensions($validator);
    }

    private function checkVersion(Validator $validator, $version)
    {
        if (version_compare($version, self::PYTHON_MIN_VERSION, '<')) {
            $validator->warn("Python version $version too old.", 'Python version ' . self::PYTHON_MIN_VERSION . ' is the minimum supported version. We recommend you update Python to a supported version.');
        }
    }

    private function checkPipVersion(Validator $validator, $version)
    {
        preg_match('/\(python ([0-9.]+)\)/', `pip3 --version 2>/dev/null`, $matches);
        $pip = $matches[1];
        $python = implode('.', array_slice(explode('.', $version), 0, 2));
        if ($pip && version_compare($python, $pip, '!=')) {
            $validator->fail("python3 ($python) and pip3 ($pip) versions do not match.  This likely will cause dependencies to be installed for the wrong python version.");
        }
    }

    private function checkExtensions(Validator $validator)
    {
        $pythonExtensions = '/scripts/dynamic_check_requirements.py';
        $process = new Process([ObzoraConfig::get('install_dir') . $pythonExtensions, '-v']);
        $process->run();

        if ($process->getExitCode() !== 0) {
            $user = \config('obzora.user');
            $user_mismatch = function_exists('posix_getpwuid') ? (posix_getpwuid(posix_geteuid())['name'] ?? null) !== $user : false;

            if ($user_mismatch) {
                $validator->warn(
                    "Could not check Python dependencies because this script is not running as $user",
                    'The install docs show how this is done on a new install: https://docs.obzora.meemtel.com/Installation/Install-ObzoraNMS/#configure-php-fpm'
                );
            } else {
                $validator->fail("Python3 module issue found: '" . $process->getOutput() . "'", 'pip3 install -r ' . ObzoraConfig::get('install_dir') . '/requirements.txt');
            }
        }
    }
}
