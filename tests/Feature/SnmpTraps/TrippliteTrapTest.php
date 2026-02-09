<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class TrippliteTrapTest extends SnmpTrapTestCase
{
    public function testTlpNotificationsAlarmEntryAdded(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:46024->[1.1.1.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:1:55:34.92
SNMPv2-MIB::snmpTrapOID.0 TRIPPLITE-PRODUCTS::tlpNotificationsAlarmEntryAdded
TRIPPLITE-PRODUCTS::tlpAlarmId 6
TRIPPLITE-PRODUCTS::tlpAlarmDescr TRIPPLITE-PRODUCTS::tlpUpsAlarmOnBattery
TRIPPLITE-PRODUCTS::tlpAlarmTime 0:1:56:20.44
TRIPPLITE-PRODUCTS::tlpAlarmTableRef TRIPPLITE-PRODUCTS::tlpDeviceTable
TRIPPLITE-PRODUCTS::tlpAlarmTableRowRef TRIPPLITE-PRODUCTS::tlpDeviceIndex.1
TRIPPLITE-PRODUCTS::tlpAlarmDetail On Battery
TRIPPLITE-PRODUCTS::tlpAlarmType warning
TRIPPLITE-PRODUCTS::tlpAlarmState active
TRIPPLITE-PRODUCTS::tlpDeviceName.1 $device->sysDescr
TRIPPLITE-PRODUCTS::tlpDeviceLocation.1 $device->location
TRIPPLITE-PRODUCTS::tlpAgentMAC.0 00:06:67:AE:BE:13
TRIPPLITE-PRODUCTS::tlpAgentUuid.0 c94e376a-8080-44fb-96ad-0fe6583d1c4a
TRAP,
            'Trap Alarm active: On Battery',
            'Could not handle tlpNotificationsAlarmEntryAdded',
            [Severity::Warning],
        );
    }

    public function testTlpNotificationsAlarmEntryRemoved(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:46024->[1.1.1.1]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:1:56:40.26
SNMPv2-MIB::snmpTrapOID.0 TRIPPLITE-PRODUCTS::tlpNotificationsAlarmEntryRemoved
TRIPPLITE-PRODUCTS::tlpAlarmId 6
TRIPPLITE-PRODUCTS::tlpAlarmDescr TRIPPLITE-PRODUCTS::tlpUpsAlarmOnBattery
TRIPPLITE-PRODUCTS::tlpAlarmTime 0:1:56:20.44
TRIPPLITE-PRODUCTS::tlpAlarmTableRef TRIPPLITE-PRODUCTS::tlpDeviceTable
TRIPPLITE-PRODUCTS::tlpAlarmTableRowRef TRIPPLITE-PRODUCTS::tlpDeviceIndex.1
TRIPPLITE-PRODUCTS::tlpAlarmDetail On Utility Power
TRIPPLITE-PRODUCTS::tlpAlarmType info
TRIPPLITE-PRODUCTS::tlpAlarmState inactive
TRIPPLITE-PRODUCTS::tlpDeviceName.1 $device->sysDescr
TRIPPLITE-PRODUCTS::tlpDeviceLocation.1 $device->location
TRIPPLITE-PRODUCTS::tlpAgentMAC.0 00:06:67:AE:BE:13
TRIPPLITE-PRODUCTS::tlpAgentUuid.0 c94e376a-8080-44fb-96ad-0fe6583d1c4a
TRAP,
            'Trap Alarm inactive: On Utility Power',
            'Could not handle tlpNotificationsAlarmEntryRemoved',
            [Severity::Info],
        );
    }
}
