<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class ApcPduOverloadTest extends SnmpTrapTestCase
{
    /**
     * Test ApcPduNearOverload trap handle
     *
     * @return void
     */
    public function testNearOverload(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 318:0:09:38.28
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::rPDUNearOverload
PowerNet-MIB::rPDUIdentSerialNumber.0 "5A1036E02224"
PowerNet-MIB::rPDUIdentName.0 "Grand POP PDU R15 A1"
PowerNet-MIB::rPDULoadStatusPhaseNumber.0 1
PowerNet-MIB::mtrapargsString.0 "Metered Rack PDU: Near overload."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'Grand POP PDU R15 A1 phase 1 Metered Rack PDU: Near overload.',
            'Could not handle rPDUNearOverload trap',
            [Severity::Warning],
        );
    }

    /**
     * Test ApcPduNearOverloadClear trap handle
     *
     * @return void
     */
    public function testNearOverloadClear(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 318:0:09:38.28
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::rPDUNearOverloadCleared
PowerNet-MIB::rPDUIdentSerialNumber.0 "5A1036E02224"
PowerNet-MIB::rPDUIdentName.0 "Grand POP PDU R15 A1"
PowerNet-MIB::rPDULoadStatusPhaseNumber.0 1
PowerNet-MIB::mtrapargsString.0 "Metered Rack PDU: Near overload cleared."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'Grand POP PDU R15 A1 phase 1 Metered Rack PDU: Near overload cleared.',
            'Could not handle rPDUNearOverloadClear trap',
            [Severity::Ok],
        );
    }

    /**
     * Test ApcPduOverload trap handle
     *
     * @return void
     */
    public function testOverload(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 318:0:09:38.28
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::rPDUOverload
PowerNet-MIB::rPDUIdentSerialNumber.0 "5A1036E02224"
PowerNet-MIB::rPDUIdentName.0 "Grand POP PDU R15 A1"
PowerNet-MIB::rPDULoadStatusPhaseNumber.0 1
PowerNet-MIB::mtrapargsString.0 "APC Rack PDU: Overload condition."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'Grand POP PDU R15 A1 phase 1 APC Rack PDU: Overload condition.',
            'Could not handle rPDUOverload trap',
            [Severity::Error],
        );
    }

    /**
     * Test ApcPduOverloadCleared trap handle
     *
     * @return void
     */
    public function testOverloadClear(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 318:0:09:38.28
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::rPDUOverloadCleared
PowerNet-MIB::rPDUIdentSerialNumber.0 "5A1036E02224"
PowerNet-MIB::rPDUIdentName.0 "Grand POP PDU R15 A1"
PowerNet-MIB::rPDULoadStatusPhaseNumber.0 1
PowerNet-MIB::mtrapargsString.0 "APC Rack PDU: Overload condition has cleared."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'Grand POP PDU R15 A1 phase 1 APC Rack PDU: Overload condition has cleared.',
            'Could not handle rPDUOverloadClear trap',
            [Severity::Ok],
        );
    }
}
