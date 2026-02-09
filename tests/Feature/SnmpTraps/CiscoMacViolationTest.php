<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class CiscoMacViolationTest extends SnmpTrapTestCase
{
    /**
     * Test CiscoMacViolation trap handle
     *
     * @return void
     */
    public function testMacViolation(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 18:30:30.32
SNMPv2-MIB::snmpTrapOID.0 CISCO-PORT-SECURITY-MIB::cpsSecureMacAddrViolation
IF-MIB::ifIndex 10104
IF-MIB::ifName GigabitEthernet1/0/24
CISCO-PORT-SECURITY-MIB::cpsIfSecureLastMacAddress a8:9d:21:e1:d8:50
TRAP,
            'SNMP Trap: Secure MAC Address Violation on port GigabitEthernet1/0/24. Last MAC address: a8:9d:21:e1:d8:50',
            'Could not handle testMacViolation trap',
            [Severity::Warning],
        );
    }
}
