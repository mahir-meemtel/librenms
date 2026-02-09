<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class ApcPowerRestoredTest extends SnmpTrapTestCase
{
    /**
     * Test ApcPowerRestored handle
     *
     * @return void
     */
    public function testApcPowerRestored(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 18:30:30.32
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::powerRestored
PowerNet-MIB::mtrapargsString.0 "INFORMATIONAL: Utility power has been restored."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'INFORMATIONAL: Utility power has been restored.',
            'Could not handle testApcPowerRestored trap',
            [Severity::Ok],
        );
    }
}
