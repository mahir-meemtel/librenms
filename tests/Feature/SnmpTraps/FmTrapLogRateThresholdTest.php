<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class FmTrapLogRateThresholdTest extends SnmpTrapTestCase
{
    public function testAvOversize(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 302:12:56:24.81
SNMPv2-MIB::snmpTrapOID.0 FORTINET-FORTIMANAGER-FORTIANALYZER-MIB::fmTrapLogRateThreshold
FORTINET-CORE-MIB::fnSysSerial.0 $device->serial
SNMPv2-MIB::sysName.0 $device->hostname
FORTINET-FORTIMANAGER-FORTIANALYZER-MIB::fmLogRate.0 315
FORTINET-FORTIMANAGER-FORTIANALYZER-MIB::fmLogRateThreshold.0 260
TRAP,
            'Recommended log rate exceeded. Current Rate: 315 Recommended Rate: 260',
            'Could not handle fmTrapLogRateThreshold trap',
            [Severity::Notice],
        );
    }
}
