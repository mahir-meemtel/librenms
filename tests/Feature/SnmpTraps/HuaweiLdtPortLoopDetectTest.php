<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class HuaweiLdtPortLoopDetectTest extends SnmpTrapTestCase
{
    /**
     * Test HuaweiLdtPortLoopDetect.php handler
     *
     * @return void
     */
    public function testHuaweiLdtPortLoopDetect(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
UDP: [{{ ip }}]:44289->[1.1.1.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 82:19:24:56.09
SNMPv2-MIB::snmpTrapOID.0 HUAWEI-LDT-MIB::hwLdtPortLoopDetect
HUAWEI-LDT-MIB::hwLPortLoopDetectIfName GigabitEthernet0/0/1
HUAWEI-LDT-MIB::hwPortLoopDetectStatus trap
HUAWEI-LDT-MIB::hwLdtPortLoopDetectVlanList none
HUAWEI-LDT-MIB::hwLdtPortLoopAutoTrapVlanList 777',
            'Loop Detected GigabitEthernet0/0/1 (Status trap, possible VLANs none, auto VLANs 777)',
            'Could not handle HUAWEI-LDT-MIB::HuaweiLdtPortLoopDetect trap',
            [Severity::Warning, 'loop', 'GigabitEthernet0/0/1']
        );
    }
}
