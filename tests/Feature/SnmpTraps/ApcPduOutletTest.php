<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class ApcPduOutletTest extends SnmpTrapTestCase
{
    public function testOutletOff(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:161->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 84:21:45:07.07
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::outletOff
PowerNet-MIB::mtrapargsInteger.0 2
PowerNet-MIB::mtrapargsString.0 \"An outlet has turned on. If the outlet number is 0, then all outlets have turned on.\"
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'APC PDU: Outlet has turned off: 2',
            'Could not handle outletOff trap',
            [Severity::Warning],
        );
    }

    public function testOutletOn(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:161->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 84:21:45:07.07
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::outletOn
PowerNet-MIB::mtrapargsInteger.0 2
PowerNet-MIB::mtrapargsString.0 \"An outlet has turned on. If the outlet number is 0, then all outlets have turned on.\"
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'APC PDU: Outlet has been turned on: 2',
            'Could not handle outletOn trap',
            [Severity::Warning],
        );
    }

    public function testOutletReboot(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:161->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 84:21:45:07.07
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::outletReboot
PowerNet-MIB::mtrapargsInteger.0 2
PowerNet-MIB::mtrapargsString.0 \"An outlet has rebooted. If the outlet number is 0, then all outlets have rebooted.\"
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'APC PDU: Outlet has rebooted: 2',
            'Could not handle outletReboot trap',
            [Severity::Warning],
        );
    }
}
