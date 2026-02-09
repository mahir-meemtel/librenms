<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class MibTest
 */
class MibTest extends TestCase
{
    /**
     * Test mib file in a directory for errors
     *
     * @param  string  $dir
     */
    #[Group('mibs')]
    #[DataProvider('mibDirs')]
    public function testMibDirectory($dir): void
    {
        $output = shell_exec('snmptranslate -M +' . ObzoraConfig::get('mib_dir') . ":$dir -m +ALL SNMPv2-MIB::system 2>&1");
        $errors = str_replace("SNMPv2-MIB::system\n", '', $output);

        $this->assertEmpty($errors, "MIBs in $dir have errors!\n$errors");
    }

    /**
     * Test that each mib only exists once.
     *
     * @param  string  $path
     * @param  string  $file
     * @param  string  $mib_name
     */
    #[Group('mibs')]
    #[DataProvider('mibFiles')]
    public function testDuplicateMibs($path, $file, $mib_name): void
    {
        global $console_color;

        $file_path = "$path/$file";
        $highligted_mib = $console_color->convert("%r$mib_name%n");

        static $existing_mibs;
        if (is_null($existing_mibs)) {
            $existing_mibs = [];
        }

        if (isset($existing_mibs[$mib_name])) {
            $existing_mibs[$mib_name][] = $file_path;

            $this->fail("$highligted_mib has duplicates: " . implode(', ', $existing_mibs[$mib_name]));
        } else {
            $existing_mibs[$mib_name] = [$file_path];
        }
    }

    /**
     * Test that the file name matches the mib name
     *
     * @param  string  $path
     * @param  string  $file
     * @param  string  $mib_name
     */
    #[Group('mibs')]
    #[DataProvider('mibFiles')]
    public function testMibNameMatches($path, $file, $mib_name): void
    {
        global $console_color;

        $file_path = "$path/$file";
        $highlighted_file = $console_color->convert("%r$file_path%n");
        $this->assertEquals($mib_name, $file, "$highlighted_file should be named $mib_name");
    }

    /**
     * Test each mib file for errors
     *
     * @param  string  $path
     * @param  string  $file
     * @param  string  $mib_name
     */
    #[Group('mibs')]
    #[DataProvider('mibFiles')]
    public function testMibContents($path, $file, $mib_name): void
    {
        global $console_color;
        $file_path = "$path/$file";
        $highlighted_file = $console_color->convert("%r$file_path%n");

        $output = shell_exec('snmptranslate -M +' . ObzoraConfig::get('mib_dir') . ":$path -m +$mib_name SNMPv2-MIB::system 2>&1");
        $errors = str_replace("SNMPv2-MIB::system\n", '', $output);

        $this->assertEmpty($errors, "$highlighted_file has errors!\n$errors");
    }

    /**
     * Get a list of all mib files with the name of the mib.
     * Called for each test that uses it before class setup.
     *
     * @return array path, filename, mib_name
     */
    public static function mibFiles(): array
    {
        $file_list = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ObzoraConfig::get('mib_dir'))) as $file) {
            /** @var SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }
            $mib_path = str_replace(ObzoraConfig::get('mib_dir') . '/', '', $file->getPathname());
            $file_list[$mib_path] = [
                str_replace(ObzoraConfig::get('install_dir'), '.', $file->getPath()),
                $file->getFilename(),
                self::extractMibName($file->getPathname()),
            ];
        }

        return $file_list;
    }

    /**
     * List all directories inside the mib directory
     *
     * @return array
     */
    public static function mibDirs(): array
    {
        $dirs = glob(ObzoraConfig::get('mib_dir') . '/*', GLOB_ONLYDIR);
        array_unshift($dirs, ObzoraConfig::get('mib_dir'));

        $final_list = [];
        foreach ($dirs as $dir) {
            $relative_dir = str_replace(ObzoraConfig::get('mib_dir') . '/', '', $dir);
            $final_list[$relative_dir] = [$dir];
        }

        return $final_list;
    }

    /**
     * Extract the mib name from a file
     *
     * @throws Exception
     */
    private static function extractMibName(string $file): string
    {
        if ($handle = fopen($file, 'r')) {
            $header = '';
            while (($line = fgets($handle)) !== false) {
                $trimmed = trim($line);

                if (empty($trimmed) || Str::startsWith($trimmed, '--')) {
                    continue;
                }

                $header .= " $trimmed";
                if (Str::contains($trimmed, 'DEFINITIONS')) {
                    preg_match('/(\S+)\s+(?=DEFINITIONS)/', $header, $matches);
                    fclose($handle);

                    return $matches[1];
                }
            }
            fclose($handle);
        }

        throw new Exception("Could not extract mib name from file ($file)");
    }
}
