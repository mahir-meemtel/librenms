<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Support\Str;
use ObzoraNMS\Data\Source\NetSnmpQuery;
use ObzoraNMS\Modules\Core;
use ObzoraNMS\Tests\Mocks\SnmpQueryMock;
use ObzoraNMS\Util\Debug;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

class OSDiscoveryTest extends TestCase
{
    private static $unchecked_files;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $glob = realpath(__DIR__ . '/..') . '/tests/snmpsim/*.snmprec';

        self::$unchecked_files = array_flip(array_filter(array_map(function ($file) {
            return basename($file, '.snmprec');
        }, glob($glob)), function ($file) {
            return ! Str::contains($file, '@');
        }));
    }

    /**
     * Populate a list of files to check and make sure it isn't empty
     */
    public function testHaveFilesToTest(): void
    {
        $this->assertNotEmpty(self::$unchecked_files);
    }

    /**
     * Test each OS provided by osProvider
     *
     * @param  string  $os_name
     */
    #[Group('os')]
    #[DataProvider('osProvider')]
    public function testOSDetection($os_name): void
    {
        if (! getenv('SNMPSIM')) {
            $this->app->bind(NetSnmpQuery::class, SnmpQueryMock::class);
        }

        $glob = ObzoraConfig::get('install_dir') . "/tests/snmpsim/$os_name*.snmprec";
        $files = array_map(function ($file) {
            return basename($file, '.snmprec');
        }, glob($glob));
        $files = array_filter($files, function ($file) use ($os_name) {
            if (Str::contains($file, '@')) {
                return false;
            }

            return $file == $os_name || Str::startsWith($file, $os_name . '_');
        });

        if (empty($files)) {
            $this->fail("No snmprec files found for $os_name!");
        }

        foreach ($files as $file) {
            $this->checkOS($os_name, $file);
            unset(self::$unchecked_files[$file]);  // This file has been tested
        }
    }

    /**
     * Test that all files have been tested (removed from self::$unchecked_files
     */
    #[Depends('testOSDetection')]
    public function testAllFilesTested(): void
    {
        $this->assertEmpty(
            self::$unchecked_files,
            'Not all snmprec files were checked: ' . print_r(array_keys(self::$unchecked_files), true)
        );
    }

    /**
     * Set up and test an os
     * If $filename is not set, it will use the snmprec file matching $expected_os
     *
     * @param  string  $expected_os  The os we should get back from getHostOS()
     * @param  string  $filename  the name of the snmprec file to use
     */
    private function checkOS($expected_os, $filename = null)
    {
        $start = microtime(true);

        $community = $filename ?: $expected_os;
        Debug::set();
        Debug::setVerbose();
        ob_start();
        $os = Core::detectOS($this->genDevice($community));
        $output = ob_get_contents();
        ob_end_clean();
        Debug::set(false);
        Debug::setVerbose(false);

        $this->assertLessThan(10, microtime(true) - $start, "OS $expected_os took longer than 10s to detect");
        $this->assertEquals($expected_os, $os, "Test file: $community.snmprec\n$output");
    }

    /**
     * Generate a fake $device array
     *
     * @param  string  $community  The snmp community to set
     * @return Device resulting device array
     */
    private function genDevice($community): Device
    {
        return new Device([
            'hostname' => $this->getSnmpsimIp(),
            'snmpver' => 'v2c',
            'port' => $this->getSnmpsimPort(),
            'timeout' => 3,
            'retries' => 0,
            'snmp_max_repeaters' => 10,
            'community' => $community,
            'os' => 'generic',
        ]);
    }

    /**
     * Provides a list of OS to generate tests.
     */
    public static function osProvider(): array
    {
        // make sure all OS are loaded
        $config_os = array_keys(ObzoraConfig::get('os'));
        if (count($config_os) < count(glob(resource_path('definitions/os_detection/*.yaml')))) {
            $config_os = array_keys(ObzoraConfig::get('os'));
        }

        $excluded_os = [
            'default',
            'generic',
            'ping',
        ];
        $filtered_os = array_diff($config_os, $excluded_os);

        $all_os = [];
        foreach ($filtered_os as $os) {
            $all_os[$os] = [$os];
        }

        return $all_os;
    }
}
