<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class ApcOnBatteryTest extends SnmpTrapTestCase
{
    /**
     * Test ApcOnBattery handle
     *
     * @return void
     */
    public function testApcOnBattery(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 18:30:30.32
SNMPv2-MIB::snmpTrapOID.0 PowerNet-MIB::upsOnBattery
PowerNet-MIB::mtrapargsString "The UPS has switched to battery backup power."
SNMPv2-MIB::snmpTrapEnterprise.0 PowerNet-MIB::apc
TRAP,
            'The UPS has switched to battery backup power.',
            'Could not handle testApcOnBattery trap',
            [Severity::Warning],
        );
    }
}
