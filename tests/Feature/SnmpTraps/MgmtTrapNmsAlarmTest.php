<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class MgmtTrapNmsAlarmTest extends SnmpTrapTestCase
{
    public function testAlarmClear(): void
    {
        $alarm = self::genEkiAlarm();

        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:60057->[192.168.1.100]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 168:19:32:11.62
SNMPv2-MIB::snmpTrapOID.0 EKINOPS-MGNT2-NMS-MIB::mgnt2TrapNMSAlarm
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogNotificationId 566098
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogObjectClassIdentifier module
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePm {$alarm['srcPm']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogBoardNumber {$alarm['slotNum']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortType Other
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortNumber 0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogProbableCause other
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSeverity cleared
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSpecificProblem {$alarm['specific']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAdditionalText 
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAlarmType synthesisAlarm
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogTime 2020-8-19,14:21:2.0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogNodeControllerIpAddress 0.0.0.0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogChassisId {{ ip }}",
            "Alarm on slot {$alarm['slotNum']}, {$alarm['srcPm']} Issue: {$alarm['specific']} Possible Cause: Unknown",
            'Could not handle mgnt2TrapNMSAlarm trap CLEARED',
            [Severity::Ok],
        );
    }

    //Test alarm with addtional text supplied.
    public function testAlarmAddText(): void
    {
        $alarm = self::genEkiAlarm();

        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:60057->[192.168.1.100]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 168:19:32:11.62
SNMPv2-MIB::snmpTrapOID.0 EKINOPS-MGNT2-NMS-MIB::mgnt2TrapNMSAlarm
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogNotificationId 566098
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogObjectClassIdentifier module
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePm {$alarm['srcPm']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogBoardNumber {$alarm['slotNum']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortType Other
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortNumber 0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogProbableCause other
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSeverity cleared
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSpecificProblem {$alarm['specific']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAdditionalText {$alarm['addText']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAlarmType synthesisAlarm
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogTime 2020-8-19,14:21:2.0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogNodeControllerIpAddress 0.0.0.0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogChassisId {{ ip }}",
            "Alarm on slot {$alarm['slotNum']}, {$alarm['srcPm']} Issue: {$alarm['specific']} Additional info: {$alarm['addText']} Possible Cause: Unknown",
            'Could not handle mgnt2TrapNMSAlarm trap with additional text',
            [Severity::Ok],
        );
    }

    //Alarm is on a specific port
    public function testAlarmPort(): void
    {
        $alarm = self::genEkiAlarm();

        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:60057->[192.168.1.100]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 168:19:32:03.51
SNMPv2-MIB::snmpTrapOID.0 EKINOPS-MGNT2-NMS-MIB::mgnt2TrapNMSAlarm
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogNotificationId 566097
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogObjectClassIdentifier port
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePm {$alarm['srcPm']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogBoardNumber {$alarm['slotNum']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortType {$alarm['portType']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSourcePortNumber {$alarm['portNum']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogProbableCause {$alarm['probCause']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSeverity critical
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogSpecificProblem {$alarm['specific']}
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAdditionalText 
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogAlarmType integrityViolation
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogTime 2020-8-19,14:20:54.0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogNodeControllerIpAddress 0.0.0.0
EKINOPS-MGNT2-NMS-MIB::mgnt2AlmLogChassisId {{ ip }}",
            "Alarm on slot {$alarm['slotNum']}, {$alarm['srcPm']} Port: {$alarm['portType']} {$alarm['portNum']} Issue: {$alarm['specific']} Possible Cause: {$alarm['probCause']}",
            'Could not handle mgnt2TrapNMSAlarm trap with additional text',
            [Severity::Error],
        );
    }

    public static function genEkiAlarm(): array
    {
        return [
            'slotNum' => rand(1, 32),
            'srcPm' => str_shuffle('0123456789abcdefg'),
            'specific' => str_shuffle('0123456789abcdefg'),
            'portType' => str_shuffle('0123456789abcdefg'),
            'probCause' => str_shuffle('0123456789abcdefg'),
            'portNum' => rand(1, 32),
            'addText' => str_shuffle('0123456789abcdefg'),
        ];
    }
}
