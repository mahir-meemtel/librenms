<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class AdvaSysAlmTrapTest extends SnmpTrapTestCase
{
    public function testCriticalAlarm(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:0:15:22.68
SNMPv2-MIB::snmpTrapOID.0 CM-ALARM-MIB::cmSysAlmTrap
CM-ALARM-MIB::cmAlmIndex.5 5
CM-ALARM-MIB::cmSysAlmNotifCode.5 critical
CM-ALARM-MIB::cmSysAlmType.5 primntpsvrFailed
CM-ALARM-MIB::cmSysAlmSrvEff.5 nonServiceAffecting
CM-ALARM-MIB::cmSysAlmTime.5 2018-12-10,11:28:20.0,-6:0
CM-ALARM-MIB::cmSysAlmLocation.5 nearEnd
CM-ALARM-MIB::cmSysAlmDirection.5 receiveDirectionOnly
CM-ALARM-MIB::cmSysAlmDescr.5 "Critical alarm test"
TRAP,
            'System Alarm: Critical alarm test Status: critical',
            'Could not handle cmSysAlmTrap critical',
            [Severity::Error],
        );
    }

    public function testMajorAlarm(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:0:15:22.68
SNMPv2-MIB::snmpTrapOID.0 CM-ALARM-MIB::cmSysAlmTrap
CM-ALARM-MIB::cmAlmIndex.5 5
CM-ALARM-MIB::cmSysAlmNotifCode.5 major
CM-ALARM-MIB::cmSysAlmType.5 primntpsvrFailed
CM-ALARM-MIB::cmSysAlmSrvEff.5 nonServiceAffecting
CM-ALARM-MIB::cmSysAlmTime.5 2018-12-10,11:28:20.0,-6:0
CM-ALARM-MIB::cmSysAlmLocation.5 nearEnd
CM-ALARM-MIB::cmSysAlmDirection.5 receiveDirectionOnly
CM-ALARM-MIB::cmSysAlmDescr.5 "Major alarm test"
TRAP,
            'System Alarm: Major alarm test Status: major',
            'Could not handle cmSysAlmTrap major',
            [Severity::Warning],
        );
    }

    public function testMinorAlarm(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:0:15:22.68
SNMPv2-MIB::snmpTrapOID.0 CM-ALARM-MIB::cmSysAlmTrap
CM-ALARM-MIB::cmAlmIndex.5 5
CM-ALARM-MIB::cmSysAlmNotifCode.5 minor
CM-ALARM-MIB::cmSysAlmType.5 primntpsvrFailed
CM-ALARM-MIB::cmSysAlmSrvEff.5 nonServiceAffecting
CM-ALARM-MIB::cmSysAlmTime.5 2018-12-10,11:28:20.0,-6:0
CM-ALARM-MIB::cmSysAlmLocation.5 nearEnd
CM-ALARM-MIB::cmSysAlmDirection.5 receiveDirectionOnly
CM-ALARM-MIB::cmSysAlmDescr.5 "Minor alarm test"
TRAP,
            'System Alarm: Minor alarm test Status: minor',
            'Could not handle cmSysAlmTrap minor',
            [Severity::Notice],
        );
    }

    public function testClearedAlarm(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:0:15:22.68
SNMPv2-MIB::snmpTrapOID.0 CM-ALARM-MIB::cmSysAlmTrap
CM-ALARM-MIB::cmAlmIndex.5 5
CM-ALARM-MIB::cmSysAlmNotifCode.5 cleared
CM-ALARM-MIB::cmSysAlmType.5 primntpsvrFailed
CM-ALARM-MIB::cmSysAlmSrvEff.5 nonServiceAffecting
CM-ALARM-MIB::cmSysAlmTime.5 2018-12-10,11:28:20.0,-6:0
CM-ALARM-MIB::cmSysAlmLocation.5 nearEnd
CM-ALARM-MIB::cmSysAlmDirection.5 receiveDirectionOnly
CM-ALARM-MIB::cmSysAlmDescr.5 "Cleared alarm test"
TRAP,
            'System Alarm: Cleared alarm test Status: cleared',
            'Could not handle cmSysAlmTrap major',
            [Severity::Ok],
        );
    }
}
