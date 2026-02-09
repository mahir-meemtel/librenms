<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class AdvaDyingGaspTrapTest extends SnmpTrapTestCase
{
    public function testDyingGasp(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CM-SYSTEM-MIB::cmSnmpDyingGaspTrap
TRAP,
            'Dying Gasp received',
            'Could not handle cmSnmpDyingGaspTrap',
            [Severity::Error],
        );
    }
}
