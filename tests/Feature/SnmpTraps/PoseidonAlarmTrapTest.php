<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class PoseidonAlarmTrapTest extends SnmpTrapTestCase
{
    /**
     * Test HWgroup Posiedon industrial environment monitoring trap handlers
     *
     * @return void
     */
    public function testPoseidonSensAlarmStateChanged(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
[UDP: [{{ ip }}]:49563->[10.2.4.101]:162]:
SNMPv2-MIB::sysUpTime.0 = Timeticks: (2940) 0:00:29.40
SNMPv2-MIB::snmpTrapOID.0 POSEIDON-MIB::sensAlarmStateChanged
POSEIDON-MIB::sensName.0 Sensor 240
POSEIDON-MIB::sensState.0 normal
POSEIDON-MIB::sensValue.0 350
POSEIDON-MIB::sensUnit.0 percent',
            'Poseidon Sensor State Change: Sensor 240 changed state to normal: 35 percent',
            'Could not handle POSEIDON-MIB::sensAlarmStateChanged test trap',
            [Severity::Ok],
        );
    }

    public function testPoseidonTsTrapAlarmEnd(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
[UDP: [{{ ip }}]:51988->[10.2.4.101]:162]:
SNMPv2-MIB::sysUpTime.0 = Timeticks: (706805) 1:57:48.05
SNMPv2-MIB::snmpTrapOID.0 POSEIDON-MIB::tsTrapAlarmEnd
POSEIDON-MIB::tsAlarmId.0 1
POSEIDON-MIB::tsAlarmDescr.0 temperatureOutOfRange',
            'Poseidon Alarm End: Alarm ID 1: temperatureOutOfRange. Check the following Poseidon Alarm State Change trap for details',
            'Could not handle POSEIDON-MIB::tsTrapAlarmEnd test trap',
            [Severity::Ok],
        );
    }

    public function testPoseidonTsTrapAlarmStart(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
[UDP: [{{ ip }}]:49563->[10.2.4.101]:162]:
SNMPv2-MIB::sysUpTime.0 = Timeticks: (101642184) 11 days, 18:20:21.84
SNMPv2-MIB::snmpTrapOID.0 POSEIDON-MIB::tsTrapAlarmStart
POSEIDON-MIB::tsAlarmId.0 1
POSEIDON-MIB::tsAlarmDescr.0 temperatureOutOfRange',
            'Poseidon Alarm Start: Alarm ID 1: temperatureOutOfRange. Check the following Poseidon Alarm State Change trap for details',
            'Could not handle POSEIDON-MIB::tsTrapAlarmStart test trap',
            [Severity::Warning],
        );
    }
}
