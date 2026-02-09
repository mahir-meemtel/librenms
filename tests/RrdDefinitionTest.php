<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use ObzoraNMS\RRD\RrdDefinition;

class RrdDefinitionTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->assertEmpty((string) new RrdDefinition());
    }

    public function testWrongType(): void
    {
        $this->expectException(\ObzoraNMS\Exceptions\InvalidRrdTypeException::class);
        ObzoraConfig::set('rrd.step', 300);
        ObzoraConfig::set('rrd.heartbeat', 600);
        $def = new RrdDefinition();
        $def->addDataset('badtype', 'Something unexpected');
    }

    public function testNameEscaping(): void
    {
        ObzoraConfig::set('rrd.step', 300);
        ObzoraConfig::set('rrd.heartbeat', 600);
        $expected = 'DS:bad_name-is_too_lon:GAUGE:600:0:100 ';
        $def = RrdDefinition::make()->addDataset('b a%d$_n:a^me-is_too_lon%g.', 'GAUGE', 0, 100, 600);

        $this->assertEquals($expected, (string) $def);
    }

    public function testCreation(): void
    {
        ObzoraConfig::set('rrd.step', 300);
        ObzoraConfig::set('rrd.heartbeat', 600);
        $expected = 'DS:pos:COUNTER:600:0:125000000000 ' .
            'DS:unbound:DERIVE:600:U:U ';

        $def = new RrdDefinition();
        $def->addDataset('pos', 'COUNTER', 0, 125000000000);
        $def->addDataset('unbound', 'DERIVE');

        $this->assertEquals($expected, $def);
    }
}
