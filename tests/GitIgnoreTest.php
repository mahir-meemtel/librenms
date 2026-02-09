<?php
namespace ObzoraNMS\Tests;

class GitIgnoreTest extends TestCase
{
    private $gitIgnoreFiles = [
        '.gitignore',
        'bootstrap/cache/.gitignore',
        'cache/.gitignore',
        'logs/.gitignore',
        'resources/views/alerts/templates/.gitignore',
        'rrd/.gitignore',
        'storage/app/.gitignore',
        'storage/app/public/.gitignore',
        'storage/debugbar/.gitignore',
        'storage/framework/cache/.gitignore',
        'storage/framework/sessions/.gitignore',
        'storage/framework/testing/.gitignore',
        'storage/framework/views/.gitignore',
        'storage/logs/.gitignore',
    ];

    public function testGitIgnoresExist(): void
    {
        foreach ($this->gitIgnoreFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    public function testGitIgnoresMode(): void
    {
        foreach ($this->gitIgnoreFiles as $file) {
            $this->assertFalse(is_executable($file), "$file should not be executable");
        }
    }

    public function testGitIgnoresNotEmpty(): void
    {
        foreach ($this->gitIgnoreFiles as $file) {
            $this->assertGreaterThan(4, filesize($file), "$file is empty, it should not be");
        }
    }
}
