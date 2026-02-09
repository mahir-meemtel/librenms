<?php
namespace ObzoraNMS\Tests\Unit;

use ObzoraNMS\Discovery\Yaml\OidField;
use ObzoraNMS\Discovery\Yaml\YamlDiscoveryField;
use ObzoraNMS\Tests\TestCase;

class YamlDiscoveryTest extends TestCase
{
    public function testYamlDiscoveryFieldCalculateValue(): void
    {
        $field = new YamlDiscoveryField('test');

        $field->calculateValue([], [], '0', 0);
        $this->assertSame(null, $field->value);

        $field->calculateValue(['test' => 'MIB::oid'], [], '0', 0);
        $this->assertSame('MIB::oid', $field->value);

        $field->calculateValue(['test' => 'MIB::oid'], ['MIB::oid' => 'value'], '0', 0);
        $this->assertSame('value', $field->value);

        $field->calculateValue(['test' => '{{ $index }} {{ MIB::oid }} {{ count }} {{ $missing }}'], ['1' => ['MIB::oid' => 'value']], '1', 0);
        $this->assertSame('1 value 0 ', $field->value);

        $field->calculateValue(['test' => '<{{ $key }}>'], ['1' => ['key' => 'value']], '1', 0);
        $this->assertSame('<value>', $field->value);

        $field->calculateValue(['test' => 'MIB::oid'], ['13' => ['MIB::oid' => 'value']], '13', 0);
        $this->assertSame('value', $field->value);

        $field->calculateValue(['test' => 'MIB::oid'], ['13' => ['MIB::oid' => 'value']], '13', 0);
        $this->assertSame('value', $field->value);

        $field = new YamlDiscoveryField('test', default: '42');
        $field->calculateValue([], ['default' => 14], '0', 0);
        $this->assertSame('42', $field->value);

        $field = new OidField('oidtest');
        $field->calculateValue(['oidtest' => '.1.1.1.1.1'], ['.1.1.1.1.1' => '-6'], '0', 0);
        $this->assertSame(-6, $field->value);

        $field->calculateValue(['oidtest' => '1'], ['.1.2' => '-6'], '0', 0);
        $this->assertSame(1, $field->value);

        $field->calculateValue(['oidtest' => 'MIB::oid'], ['2.3' => ['MIB::oid' => '43%']], '2.3', 0);
        $this->assertSame(43, $field->value);

        $field->calculateValue(['oidtest' => 'missing'], ['2.3' => ['MIB::oid' => '41']], '2.3', 0);
        $this->assertSame(null, $field->value);

        $field->calculateValue(['oidtest' => 'missing'], ['2.3' => ['MIB::oid' => 'non-numeric']], '2.3', 0);
        $this->assertSame(null, $field->value);
    }
}
