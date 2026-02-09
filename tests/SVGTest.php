<?php
namespace ObzoraNMS\Tests;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class SVGTest
 */
#[Group('os')]
class SVGTest extends TestCase
{
    public function testSVGContainsPNG(): void
    {
        foreach ($this->getSvgFiles() as $file => $_unused) {
            $svg = file_get_contents($file);

            $this->assertFalse(
                Str::contains($svg, 'data:image/'),
                "$file contains a bitmap image, please use a regular png or valid svg"
            );
        }
    }

    public function testSVGHasLengthWidth(): void
    {
        foreach ($this->getSvgFiles() as $file => $_unused) {
            if ($file == 'html/images/safari-pinned-tab.svg') {
                continue;
            }

            if (str_starts_with($file, 'html/images/custommap/background/')) {
                continue;
            }

            $svg = file_get_contents($file);

            $this->assertEquals(
                0,
                preg_match('/<svg[^>]*(length|width)=/', $svg, $matches),
                "$file: SVG files must not contain length or width attributes "
            );
        }
    }

    public function testSVGHasViewBox(): void
    {
        foreach ($this->getSvgFiles() as $file => $_unused) {
            $svg = file_get_contents($file);

            $this->assertTrue(
                Str::contains($svg, 'viewBox'),
                "$file: SVG files must have the viewBox attribute set"
            );
        }
    }

    private function getSvgFiles(): RegexIterator
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('html/images'));

        return new RegexIterator($iterator, '/^.+\.svg$/i', RecursiveRegexIterator::GET_MATCH);
    }
}
