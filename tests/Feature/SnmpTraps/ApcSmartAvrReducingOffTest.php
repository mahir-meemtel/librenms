<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class ApcSmartAvrReducingOffTest extends SnmpTrapTestCase
{
    /**
     * Test ApcSmartAvrReducingOff handle
     *
     * @return void
     */
    public function testApcSmartAvrReducingOff(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 459:20:47:26.90
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::smartAvrReducingOff
PowerNet-MIB::mtrapargsString "UPS: No longer compensating for a high input voltage."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'UPS: No longer compensating for a high input voltage.',
            'Could not handle testApcSmartAvrReducingOff trap',
            [Severity::Ok],
        );
    }
}
