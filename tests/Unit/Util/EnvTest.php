<?php
namespace ObzoraNMS\Tests\Unit\Util;

use ObzoraNMS\Tests\TestCase;
use ObzoraNMS\Util\EnvHelper;

class EnvTest extends TestCase
{
    public function testParseArray(): void
    {
        putenv('PARSETEST=one,two');
        $this->assertSame(['one', 'two'], EnvHelper::parseArray('PARSETEST'), 'Could not parse simple array');
        $this->assertSame(['default'], EnvHelper::parseArray('PARSETESTNOTSET', 'default'), 'Did not get default value as expected');
        $this->assertSame(null, EnvHelper::parseArray('PARSETESTNOTSET'), 'Did not get null as expected when env not set');
        $this->assertSame(3, EnvHelper::parseArray('PARSETESTNOTSET', 3), 'Did not get default value (non-array) as expected');
        $this->assertSame('default', EnvHelper::parseArray('PARSETESTNOTSET', 'default', ['default']), 'Did not get default value as expected, excluding it from exploding');

        putenv('PARSETEST=');
        $this->assertSame([''], EnvHelper::parseArray('PARSETEST', null, []), 'Did not get empty string as expected when env set to empty');

        putenv('PARSETEST=*');
        $this->assertSame('*', EnvHelper::parseArray('PARSETEST', null, ['*', '*']), 'Did not properly ignore exclude values');

        // clean the environment
        putenv('PARSETEST');
    }

    public function testSetEnv(): void
    {
        $this->assertEquals("ONE=one\nTWO=2\$\nTHREE=\"space space\"\n", EnvHelper::setEnv("ONE=one\nTWO=\n", [
            'ONE' => 'zero',
            'TWO' => '2$',
            'THREE' => 'space space',
        ]));

        $this->assertEquals("A=a\nB=b\nC=c\nD=d\n", EnvHelper::setEnv("#A=\nB=b\nF=blah\nC=\n", [
            'C' => 'c',
            'D' => 'd',
            'B' => 'nope',
            'A' => 'a',
        ], ['F', 'A']));

        // replace
        $this->assertEquals("#COMMENT=something\nCOMMENT=else\n", EnvHelper::setEnv("COMMENT=nothing\n#COMMENT=something", [
            'COMMENT' => 'else',
        ], ['COMMENT']));
    }
}
