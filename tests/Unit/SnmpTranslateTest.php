<?php
namespace ObzoraNMS\Tests\Unit;

use App\Models\Device;
use ObzoraNMS\Tests\TestCase;

class SnmpTranslateTest extends TestCase
{
    public function testSimpleInput(): void
    {
        $actual = \SnmpQuery::numeric()->translate('IF-MIB::ifTable');
        $this->assertEquals('.1.3.6.1.2.1.2.2', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['IF-MIB'], append: false)->translate('ifTable');
        $this->assertEquals('.1.3.6.1.2.1.2.2', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['ALL'], append: false)->translate('ifTable');
        $this->assertEquals('.1.3.6.1.2.1.2.2', $actual);

        $actual = \SnmpQuery::translate('IF-MIB::ifTable');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        $actual = \SnmpQuery::numeric()->translate('.1.3.6.1.2.1.2.2');
        $this->assertEquals('.1.3.6.1.2.1.2.2', $actual);

        $actual = \SnmpQuery::mibs(['IF-MIB'])->translate('.1.3.6.1.2.1.2.2');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        $actual = \SnmpQuery::numeric()->translate('1.3.6.1.2.1.2.2');
        $this->assertEquals('.1.3.6.1.2.1.2.2', $actual);

        $actual = \SnmpQuery::mibs(['ALL'], append: false)->translate('.1.3.6.1.2.1.2.2');
        $this->assertEquals('RFC1213-MIB::ifTable', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['IP-MIB'])->translate('ifTable');
        $this->assertEquals('.1.3.6.1.2.1.2.2', $actual);

        $actual = \SnmpQuery::mibs(['IP-MIB'])->translate('ifTable');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        // with index
        $actual = \SnmpQuery::numeric()->translate('IF-MIB::ifTable.0');
        $this->assertEquals('.1.3.6.1.2.1.2.2.0', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['IF-MIB'])->translate('ifTable.0');
        $this->assertEquals('.1.3.6.1.2.1.2.2.0', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['ALL'], append: false)->translate('ifTable.0');
        $this->assertEquals('.1.3.6.1.2.1.2.2.0', $actual);

        $actual = \SnmpQuery::translate('IF-MIB::ifTable.0');
        $this->assertEquals('IF-MIB::ifTable.0', $actual);

        $actual = \SnmpQuery::numeric()->translate('.1.3.6.1.2.1.2.2.0');
        $this->assertEquals('.1.3.6.1.2.1.2.2.0', $actual);

        $actual = \SnmpQuery::mibs(['IF-MIB'])->translate('.1.3.6.1.2.1.2.2.0');
        $this->assertEquals('IF-MIB::ifTable.0', $actual);

        $actual = \SnmpQuery::numeric()->translate('1.3.6.1.2.1.2.2.0');
        $this->assertEquals('.1.3.6.1.2.1.2.2.0', $actual);

        $actual = \SnmpQuery::mibs(['ALL'], append: false)->translate('.1.3.6.1.2.1.2.2.0');
        $this->assertEquals('RFC1213-MIB::ifTable.0', $actual);

        $actual = \SnmpQuery::mibs(['IP-MIB'])->translate('ifTable.0');
        $this->assertEquals('IF-MIB::ifTable.0', $actual);

        $actual = \SnmpQuery::mibs(['SNMPv2-MIB'])->translate('iso.3.6.1.2.1.1.1.0');
        $this->assertEquals('SNMPv2-MIB::sysDescr.0', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['SNMPv2-MIB'])->translate('iso.3.6.1.2.1.1.1.0');
        $this->assertEquals('.1.3.6.1.2.1.1.1.0', $actual);
    }

    public function testFailedInput(): void
    {
        $actual = \SnmpQuery::translate('ifTable');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        $actual = \SnmpQuery::numeric()->mibs([], append: false)->translate('ifTable');
        $this->assertEquals('', $actual);

        $actual = \SnmpQuery::mibs([], append: false)->translate('ifTable');
        $this->assertEquals('', $actual);

        $actual = \SnmpQuery::numeric()->mibs(['ASDF-MIB', 'SNMPv2-MIB'], append: false)->translate('ifTable');
        $this->assertEquals('', $actual);

        $actual = \SnmpQuery::mibs([], append: false)->translate('ifTable');
        $this->assertEquals('', $actual);

        $actual = \SnmpQuery::numeric()->mibs([], append: false)->translate('ifTable');
        $this->assertEquals('', $actual);

        $actual = \SnmpQuery::mibs([], append: false)->translate('ifTable');
        $this->assertEquals('', $actual);
    }

    public function testComplexInput(): void
    {
        $actual = \SnmpQuery::mibs(['RFC1213-MIB', 'IF-MIB'], append: false)->translate('.1.3.6.1.2.1.2.2');
        $this->assertEquals('RFC1213-MIB::ifTable', $actual);

        $actual = \SnmpQuery::mibs(['IF-MIB', 'RFC1213-MIB'], append: false)->translate('.1.3.6.1.2.1.2.2');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        $actual = \SnmpQuery::translate('ifTable');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        $actual = \SnmpQuery::mibs(['RFC1213-MIB', 'IF-MIB'], append: false)->translate('ifTable');
        $this->assertEquals('RFC1213-MIB::ifTable', $actual);

        $actual = \SnmpQuery::mibs(['IF-MIB', 'RFC1213-MIB'], append: false)->translate('ifTable');
        $this->assertEquals('IF-MIB::ifTable', $actual);

        // partial numeric
        $device = Device::factory()->make(['os' => 'dlink']);
        $actual = \SnmpQuery::device($device)->numeric()->mibs(['EQUIPMENT-MIB', 'DLINKSW-ENTITY-EXT-MIB'], append: false)->translate('.1.3.6.1.4.1.171.14.5.1.4.1.4.1.dram');
        $this->assertEquals('.1.3.6.1.4.1.171.14.5.1.4.1.4.1.1', $actual);

        $actual = \SnmpQuery::device($device)->numeric()->mibs(['EQUIPMENT-MIB', 'DLINKSW-ENTITY-EXT-MIB'], append: false)->translate('iso.3.6.1.4.1.171.14.5.1.4.1.4.1.dram');
        $this->assertEquals('.1.3.6.1.4.1.171.14.5.1.4.1.4.1.1', $actual);
    }
}
