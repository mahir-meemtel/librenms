<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class AxisVideoTrapTest extends SnmpTrapTestCase
{
    /**
     * Test Axis Video trap handlers
     *
     * @return void
     */
    public function testAxisAlarmNew(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
[UDP: [{{ ip }}]:49563->[10.2.4.101]:162]:
SNMPv2-MIB::sysUpTime.0 = Timeticks: (2940) 0:00:29.40
SNMPv2-MIB::snmpTrapOID.0 AXIS-VIDEO-MIB::alarmNew
AXIS-VIDEO-MIB::alarmID 1
AXIS-VIDEO-MIB::alarmName Edge Storage Operation
AXIS-VIDEO-MIB::alarmText Check the SD_DISK storage device',
            'Axis Alarm Trap: Alarm ID 1: Edge Storage Operation: Check the SD_DISK storage device',
            'Could not handle AXIS-VIDEO-MIB::alarmNew test trap',
            [Severity::Warning],
        );
    }

    public function testAxisAlarmCleared(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
[UDP: [{{ ip }}]:51988->[10.2.4.101]:162]:
SNMPv2-MIB::sysUpTime.0 = Timeticks: (706805) 1:57:48.05
SNMPv2-MIB::snmpTrapOID.0 AXIS-VIDEO-MIB::alarmCleared
AXIS-VIDEO-MIB::alarmID 2
AXIS-VIDEO-MIB::alarmName Fan Operation
AXIS-VIDEO-MIB::alarmText Check the fan',
            'Axis Alarm Cleared Trap: Alarm ID 2 for Fan Operation with text "Check the fan" has cleared',
            'Could not handle AXIS-VIDEO-MIB::alarmCleared test trap',
            [Severity::Ok],
        );
    }

    public function testAxisAlarmSingle(): void
    {
        $this->assertTrapLogsMessage('{{ hostname }}
[UDP: [{{ ip }}]:49563->[10.2.4.101]:162]:
SNMPv2-MIB::sysUpTime.0 = Timeticks: (101642184) 11 days, 18:20:21.84
SNMPv2-MIB::snmpTrapOID.0 AXIS-VIDEO-MIB::alarmSingle
AXIS-VIDEO-MIB::alarmID 3
AXIS-VIDEO-MIB::alarmName Camera Tampering
AXIS-VIDEO-MIB::alarmText Check if the camera is blocked, redirected or defocused',
            'Axis Alarm Trap: Alarm ID 3: Camera Tampering: Check if the camera is blocked, redirected or defocused',
            'Could not handle AXIS-VIDEO-MIB::alarmASingle test trap',
            [Severity::Warning],
        );
    }
}
