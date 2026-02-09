<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class HuaweiLdtPortLoopDetectRecoveryTest extends SnmpTrapTestCase
{
    /**
     * Test HuaweiLdtPortLoopRecoveryDetect.php handler
     *
     * @return void
     */
    public function testHuaweiLdtPortLoopRecoveryDetect(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
UDP: [{{ ip }}]:44289->[1.1.1.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 82:19:24:56.09
SNMPv2-MIB::snmpTrapOID.0 HUAWEI-LDT-MIB::hwLdtPortLoopDetectRecovery
HUAWEI-LDT-MIB::hwLPortLoopDetectIfName GigabitEthernet0/0/1
HUAWEI-LDT-MIB::hwPortLoopDetectStatus normal',
            'Loop Detect Recovery GigabitEthernet0/0/1 (Status normal)',
            'Could not handle HUAWEI-LDT-MIB::HuaweiLdtPortLoopDetectRecovery trap',
            [Severity::Ok, 'loop', 'GigabitEthernet0/0/1']
        );
    }
}
