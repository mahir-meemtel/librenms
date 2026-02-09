<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class CppmServiceStopTrapTest extends SnmpTrapTestCase
{
    public function testServiceStop(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 CPPM-MIB::cppmServiceStopNotification
CPPM-MIB::cppmServiceName.0 "cpass-radius-server"
CPPM-MIB::cppmTrapMessage.0 "cpass-radius-server service is stopped"

TRAP,
            'Clearpass Service Trap - Host:{{ hostname }} Service:cpass-radius-server Message:cpass-radius-server service is stopped',
            'Could not handle cppmServiceStopNotification',
            [Severity::Warning],
        );
    }
}
