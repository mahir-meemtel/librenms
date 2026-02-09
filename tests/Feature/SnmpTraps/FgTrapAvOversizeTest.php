<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

class FgTrapAvOversizeTest extends SnmpTrapTestCase
{
    public function testAvOversize(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 302:12:56:24.81
SNMPv2-MIB::snmpTrapOID.0 FORTINET-FORTIGATE-MIB::fgTrapAvOversize
FORTINET-CORE-MIB::fnSysSerial.0 $device->serial
SNMPv2-MIB::sysName.0 $device->hostname
TRAP,
            '{{ hostname }} received a file that exceeds proxy buffer, skipping AV scan',
            'Could not handle fgTrapIpsAvOversize',
        );
    }
}
