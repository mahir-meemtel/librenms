<?php
namespace ObzoraNMS\Tests\Unit\Util;

use ObzoraNMS\Tests\TestCase;
use ObzoraNMS\Util\StringHelpers;

class StringHelperTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testInferEncoding(): void
    {
        $this->assertEquals(null, StringHelpers::inferEncoding(null));
        $this->assertEquals('', StringHelpers::inferEncoding(''));
        $this->assertEquals('~null', StringHelpers::inferEncoding('~null'));
        $this->assertEquals('Øverbyvegen', StringHelpers::inferEncoding('Øverbyvegen'));

        $this->assertEquals('Øverbyvegen', StringHelpers::inferEncoding(base64_decode('w5h2ZXJieXZlZ2Vu')));
        $this->assertEquals('Øverbyvegen', StringHelpers::inferEncoding(base64_decode('2HZlcmJ5dmVnZW4=')));

        config(['app.charset' => 'Shift_JIS']);
        $this->assertEquals('コンサート', StringHelpers::inferEncoding(base64_decode('g1KDk4NUgVuDZw==')));
    }

    public function testIsStringable(): void
    {
        $this->assertTrue(StringHelpers::isStringable(null));
        $this->assertTrue(StringHelpers::isStringable(''));
        $this->assertTrue(StringHelpers::isStringable('string'));
        $this->assertTrue(StringHelpers::isStringable(-1));
        $this->assertTrue(StringHelpers::isStringable(1.0));
        $this->assertTrue(StringHelpers::isStringable(false));

        $this->assertFalse(StringHelpers::isStringable([]));
        $this->assertFalse(StringHelpers::isStringable((object) []));

        $stringable = new class
        {
            public function __toString()
            {
                return '';
            }
        };
        $this->assertTrue(StringHelpers::isStringable($stringable));

        $nonstringable = new class {
        };
        $this->assertFalse(StringHelpers::isStringable($nonstringable));
    }

    public function testIsHexString(): void
    {
        $this->assertTrue(StringHelpers::isHex('af'));
        $this->assertTrue(StringHelpers::isHex('28'));
        $this->assertTrue(StringHelpers::isHex('aF28'));
        $this->assertFalse(StringHelpers::isHex('a'));
        $this->assertFalse(StringHelpers::isHex('aF 28'));
        $this->assertFalse(StringHelpers::isHex('aF 2'));
        $this->assertFalse(StringHelpers::isHex('aG'));
    }

    public function testIsHexWithDelimiters(): void
    {
        $this->assertTrue(StringHelpers::isHex('af 28 02', ' '));
        $this->assertTrue(StringHelpers::isHex('aF 28 02 CE', ' '));
        $this->assertFalse(StringHelpers::isHex('a5 fj 53', ' '));
        $this->assertFalse(StringHelpers::isHex('a5fe53', ' '));

        $this->assertFalse(StringHelpers::isHex('af 28 02', ':'));
        $this->assertTrue(StringHelpers::isHex('af:28:02', ':'));
        $this->assertTrue(StringHelpers::isHex('aF:28:02:CE', ':'));
        $this->assertFalse(StringHelpers::isHex('a5:fj:53', ':'));
        $this->assertFalse(StringHelpers::isHex('a5fe53', ':'));
    }
}
