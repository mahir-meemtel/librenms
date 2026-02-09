<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class CppmServiceStartTrapTest extends SnmpTrapTestCase
{
    public function testServiceStart(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CPPM-MIB::cppmServiceStartNotification
CPPM-MIB::cppmServiceName.0 "cpass-radius-server"
CPPM-MIB::cppmTrapMessage.0 "cpass-radius-server service is started"

TRAP,
            'Clearpass Service Trap - Host:{{ hostname }} Service:cpass-radius-server Message:cpass-radius-server service is started',
            'Could not handle cppmServiceStartNotification',
            [Severity::Warning],
        );
    }
}
