<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class JnxPowerSupplyTest extends SnmpTrapTestCase
{
    public function testJnxPowerSupplyFailureTrap(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:49716->[10.0.0.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 470:23:25:41.21
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-MIB::jnxPowerSupplyFailure
JUNIPER-MIB::jnxContentsContainerIndex.2.4.0.0 2
JUNIPER-MIB::jnxContentsL1Index.2.4.0.0 4
JUNIPER-MIB::jnxContentsL2Index.2.4.0.0 0
JUNIPER-MIB::jnxContentsL3Index.2.4.0.0 0
JUNIPER-MIB::jnxContentsDescr.2.4.0.0 PEM 3
JUNIPER-MIB::jnxOperatingState.2.4.0.0 down
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX960
TRAP,
            'Power Supply PEM 3 is down',
            'Could not handle JnxPowerSupplyFailure',
            [Severity::Error],
        );
    }

    public function testJnxPowerSupplyOkTrap(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:49716->[10.0.0.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 470:23:25:41.21
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-MIB::jnxPowerSupplyOK
JUNIPER-MIB::jnxContentsContainerIndex.2.4.0.0 2
JUNIPER-MIB::jnxContentsL1Index.2.4.0.0 4
JUNIPER-MIB::jnxContentsL2Index.2.4.0.0 0
JUNIPER-MIB::jnxContentsL3Index.2.4.0.0 0
JUNIPER-MIB::jnxContentsDescr.2.4.0.0 PEM 4
JUNIPER-MIB::jnxOperatingState.2.4.0.0 ok
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameMX960
TRAP,
            'Power Supply PEM 4 is OK',
            'Could not handle JnxPowerSupplyOK',
            [Severity::Ok],
        );
    }
}
