<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

class AdvaObjectDeletionTest extends SnmpTrapTestCase
{
    public function testUserDeletion(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CM-SYSTEM-MIB::cmObjectDeletionTrap
CM-SECURITY-MIB::cmSecurityUserName.\"testuser\".false testuser
RMON2-MIB::probeDateTime.0 \"07 E2 0C 0A 08 38 1B 00 2D 06 00 \"
ADVA-MIB::neEventLogIndex.92 92
ADVA-MIB::neEventLogTimeStamp.92 2018-12-10,8:56:27.5,-6:0
TRAP,
            'User object testuser deleted',
            'Could not handle cmObjectDeletionTrap user deletion',
        );
    }

    public function testFLowDeletion(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CM-SYSTEM-MIB::cmObjectDeletionTrap
CM-FACILITY-MIB::cmFlowIndex.1.1.1.4.1 1
RMON2-MIB::probeDateTime.0 \"07 E2 0C 0A 09 07 1C 00 2D 06 00 \"
ADVA-MIB::neEventLogIndex.148 148
ADVA-MIB::neEventLogTimeStamp.148 2018-12-10,9:7:28.1,-6:0
TRAP,
            'Flow 1-1-1-4-1 deleted',
            'Could not handle cmObjectDeletionTrap flow deletion',
        );
    }

    public function testLagPortDeletion(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CM-SYSTEM-MIB::cmObjectDeletionTrap
F3-LAG-MIB::f3LagPortIndex.1.1.1 1
RMON2-MIB::probeDateTime.0 \"07 E2 0C 0A 09 03 33 00 2D 06 00 \"
ADVA-MIB::neEventLogIndex.136 136
ADVA-MIB::neEventLogTimeStamp.136 2018-12-10,9:3:51.3,-6:0
TRAP,
            'LAG member port 1 removed from LAG 1-1',
            'Could not handle cmObjectDeletionTrap LAG port deletion',
        );
    }

    public function testLagDeletion(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CM-SYSTEM-MIB::cmObjectDeletionTrap
F3-LAG-MIB::f3LagIndex.1.1 1
RMON2-MIB::probeDateTime.0 \"07 E2 0C 0A 09 03 33 00 2D 06 00 \"
ADVA-MIB::neEventLogIndex.139 139
ADVA-MIB::neEventLogTimeStamp.139 2018-12-10,9:3:51.4,-6:0
TRAP,
            'LAG 1 deleted',
            'Could not handle cmObjectDeletionTrap LAG deletion',
        );
    }
}
