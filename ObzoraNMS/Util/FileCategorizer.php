<?php
namespace ObzoraNMS\Util;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class FileCategorizer extends Categorizer
{
    private const TESTS_REGEX = '#^tests/(snmpsim|data)/(([0-9a-z\-]+)(_[0-9a-z\-]+)?)(_[0-9a-z\-]+)?\.(json|snmprec)$#';

    public function __construct($items = [])
    {
        parent::__construct($items);

        if (getenv('CIHELPER_DEBUG')) {
            $this->setSkippable(function ($item) {
                return in_array($item, [
                    '.github/workflows/test.yml',
                    'ObzoraNMS/Util/CiHelper.php',
                    'ObzoraNMS/Util/FileCategorizer.php',
                    'app/Console/Commands/DevCheckCommand.php',
                    'tests/Unit/CiHelperTest.php',
                ]);
            });
        }

        $this->addCategory('php', function ($item) {
            return Str::endsWith($item, '.php') ? $item : false;
        });
        $this->addCategory('docs', function ($item) {
            return (Str::startsWith($item, 'doc/') || $item == 'mkdocs.yml') ? $item : false;
        });
        $this->addCategory('python', function ($item) {
            return Str::endsWith($item, '.py') ? $item : false;
        });
        $this->addCategory('bash', function ($item) {
            return Str::endsWith($item, '.sh') ? $item : false;
        });
        $this->addCategory('svg', function ($item) {
            return Str::endsWith($item, '.svg') ? $item : false;
        });
        $this->addCategory('resources', function ($item) {
            return (str_starts_with($item, 'resources/') && ! str_starts_with($item, 'resources/definitions/os_')) ? $item : false;
        });
        $this->addCategory('full-checks', function ($item) {
            return in_array($item, ['composer.lock', '.github/workflows/test.yml']) ? $item : false;
        });
        $this->addCategory('os-files', function ($item) {
            if (($os_name = $this->osFromFile($item)) !== null) {
                return ['os' => $os_name, 'file' => $item];
            }

            return false;
        });
    }

    public function categorize()
    {
        // This can't be a normal addCategory() function since it returns multiple results
        $this->osFromMibs();
        parent::categorize();

        // split out os
        $this->categorized['os'] = array_unique(array_column($this->categorized['os-files'], 'os'));
        $this->categorized['os-files'] = array_column($this->categorized['os-files'], 'file');

        // If we have more than 4 (arbitrary number) of OS' then blank them out
        // Unit tests may take longer to run in a loop so fall back to all.
        if (count($this->categorized['os']) > 4) {
            $this->categorized['full-checks'] = [true];
        }

        return $this->categorized;
    }

    private function validateOs($os)
    {
        return file_exists("resources/definitions/os_detection/$os.yaml") ? $os : null;
    }

    private function osFromMibs(): void
    {
        $mibs = [];

        foreach ($this->items as $file) {
            if (Str::startsWith($file, 'mibs/')) {
                $mibs[] = basename($file, '.mib');
            }
        }

        if (empty($mibs)) {
            return;
        }

        $grep = new Process(
            [
                'grep',
                '--fixed-strings',
                '--recursive',
                '--files-with-matches',
                '--file=-',
                '--',
                'resources/definitions/os_',
                'includes/discovery/',
                'includes/polling/',
                'ObzoraNMS/OS/',
            ],
            null,
            null,
            implode("\n", $mibs)
        );

        $grep->run();

        foreach (explode("\n", trim($grep->getOutput())) as $item) {
            if (($os_name = $this->osFromFile($item)) !== null) {
                $this->categorized['os-files'][] = ['os' => $os_name, 'file' => $item];
            }
        }
    }

    private function osFromFile($file)
    {
        if (Str::startsWith($file, 'resources/definitions/os_')) {
            return basename($file, '.yaml');
        } elseif (Str::startsWith($file, ['includes/polling', 'includes/discovery'])) {
            return $this->validateOs(basename($file, '.inc.php'));
        } elseif (preg_match('#ObzoraNMS/OS/[^/]+.php#', $file)) {
            return $this->osFromClass(basename($file, '.php'));
        } elseif (preg_match(self::TESTS_REGEX, $file, $matches)) {
            if ($this->validateOs($matches[3])) {
                return $matches[3];
            }
            if ($this->validateOs($matches[2])) {
                return $matches[2];
            }
        }

        return null;
    }

    /**
     * convert class name to os name
     *
     * @param  string  $class
     * @return string|null
     */
    private function osFromClass($class)
    {
        preg_match_all('/[A-Z][a-z0-9]*/', $class, $segments);
        $osname = implode('-', array_map('strtolower', $segments[0]));
        $osname = preg_replace(
            ['/^zero-/', '/^one-/', '/^two-/', '/^three-/', '/^four-/', '/^five-/', '/^six-/', '/^seven-/', '/^eight-/', '/^nine-/'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            $osname
        );

        if ($os = $this->validateOs($osname)) {
            return $os;
        }

        return $this->validateOs(str_replace('-', '_', $osname));
    }
}
