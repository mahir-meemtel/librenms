<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class RuckusSzClusterStateTest extends SnmpTrapTestCase
{
    public function testClusterInMaintenance(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 RUCKUS-SZ-EVENT-MIB::ruckusSZClusterInMaintenanceStateTrap
RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity.0 "Critical"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventCode.0 "807"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventType.0 "clusterInMaintenanceState"
RUCKUS-SZ-EVENT-MIB::ruckusSZClusterName.0 "{{ hostname }}"
TRAP,
            'Smartzone cluster {{ hostname }} state changed to maintenance',
            'Could not handle ruckusSZClusterInMaintenanceStateTrap',
            [Severity::Notice],
        );
    }

    public function testClusterInService(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 RUCKUS-SZ-EVENT-MIB::ruckusSZClusterBackToInServiceTrap
RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity.0 "Informational"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventCode.0 "808"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventType.0 "clusterBackToInService"
RUCKUS-SZ-EVENT-MIB::ruckusSZClusterName.0 "{{ hostname }}"
TRAP,
            'Smartzone cluster {{ hostname }} is now in service',
            'Could not handle ruckusSZClusterBackToInServiceTrap',
        );
    }
}
