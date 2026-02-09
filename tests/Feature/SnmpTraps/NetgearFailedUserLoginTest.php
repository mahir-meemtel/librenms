<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class NetgearFailedUserLoginTest extends SnmpTrapTestCase
{
    public function testManagedSeries(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:44298->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 NETGEAR-SWITCHING-MIB::failedUserLoginTrap
TRAP,
            'SNMP Trap: Failed User Login: {{ hostname }}',
            'Could not handle NETGEAR-SWITCHING-MIB::failedUserLoginTrap trap',
            [Severity::Warning, 'auth'],
        );
    }

    public function testSmartSeries(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:1026->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 30:22:57:58.00
SNMPv2-MIB::snmpTrapOID.0 NETGEAR-SMART-SWITCHING-MIB::failedUserLoginTrap
TRAP,
            'SNMP Trap: Failed User Login: {{ hostname }}',
            'Could not handle NETGEAR-SMART-SWITCHING-MIB::failedUserLoginTrap trap',
            [Severity::Warning, 'auth'],
        );
    }
}
