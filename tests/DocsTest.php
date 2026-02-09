<?php
namespace ObzoraNMS\Tests;

use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Yaml\Yaml;

class DocsTest extends TestCase
{
    private $hidden_pages = [
    ];

    #[Group('docs')]
    public function testDocExist(): void
    {
        $mkdocs = Yaml::parse(file_get_contents(__DIR__ . '/../mkdocs.yml'));
        $dir = __DIR__ . '/../doc/';

        // Define paths to exclude
        $exclude_paths = [
            '*/Extensions/Applications/*',
            '*/General/Changelogs/*',
            '*/Alerting/Transports/*',
        ];

        // Build the exclusion part of the find command
        $exclude_conditions = implode(' -not -path ', array_map(fn ($path) => escapeshellarg($path), $exclude_paths));
        $find_command = "find $dir -name '*.md' -not -path $exclude_conditions";

        // Run the find command with exclusions
        $files = str_replace($dir, '', rtrim(`$find_command`));

        // Check for missing pages
        collect(explode(PHP_EOL, $files))
            ->diff(collect($mkdocs['nav'])->flatten()->merge($this->hidden_pages)) // grab defined pages and diff
            ->each(function ($missing_doc) {
                $this->fail("The doc $missing_doc doesn't exist in mkdocs.yml, please add it to the relevant section");
            });

        $this->expectNotToPerformAssertions();
    }
}
