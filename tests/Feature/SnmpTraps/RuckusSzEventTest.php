<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class RuckusSzEventTest extends SnmpTrapTestCase
{
    public function testSzApConf(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 RUCKUS-SZ-EVENT-MIB::ruckusSZAPConfUpdatedTrap
RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity.0 "Informational"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventCode.0 "110"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventType.0 "apConfUpdated"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPName.0 "{{ hostname }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPMacAddr.0 "de:ad:be:ef:33:40"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPIP.0 "{{ ip }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPLocation.0 "{{ location }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPDescription.0 "{{ sysDescr }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZAPConfigID.0 "2f860f70-6b88-11e9-a3c5-000000937916"
TRAP,
            'AP at location {{ location }} configuration updated with config-id 2f860f70-6b88-11e9-a3c5-000000937916',
            'Could not handle ruckusSZAPConfUpdatedTrap',
            [Severity::Info],
        );
    }

    public function testSzApConnect(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 RUCKUS-SZ-EVENT-MIB::ruckusSZAPConnectedTrap
RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity.0 "Informational"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventCode.0 "312"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventType.0 "apConnected"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPName.0 "{{ hostname }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPMacAddr.0 "de:ad:be:ef:33:40"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPIP.0 "{{ ip }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPLocation.0 "{{ location }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPDescription.0 "{{ sysDescr }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventReason.0 "AP connected after rebooting"
TRAP,
            'AP at location {{ location }} has connected to the SmartZone with reason AP connected after rebooting',
            'Could not handle ruckusSZAPConnectedTrap',
            [Severity::Info],
        );
    }

    public function testSzApMiscEvent(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 RUCKUS-SZ-EVENT-MIB::ruckusSZAPMiscEventTrap
RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity.0 "Minor"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventCode.0 "322"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventType.0 "apWLANStateChanged"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPName.0 "{{ hostname }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPMacAddr.0 "de:ad:be:ef:33:40"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPIP.0 "{{ ip }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPLocation.0 "{{ location }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPDescription.0 "{{ sysDescr }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventDescription.0 "Test AP event has occurred"
TRAP,
            'AP event: Test AP event has occurred',
            'Could not handle ruckusSZAPMiscEventTrap',
            [Severity::Warning],
        );
    }

    public function testSzApRebooted(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 26:19:43:37.24
SNMPv2-MIB::snmpTrapOID.0 RUCKUS-SZ-EVENT-MIB::ruckusSZAPRebootTrap
RUCKUS-SZ-EVENT-MIB::ruckusSZEventSeverity.0 "Critical"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventCode.0 "301"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventType.0 "apRebootByUser"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPName.0 "{{ hostname }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPMacAddr.0 "de:ad:be:ef:33:40"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPIP.0 "{{ ip }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPLocation.0 "{{ location }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventAPDescription.0 "{{ sysDescr }}"
RUCKUS-SZ-EVENT-MIB::ruckusSZEventReason.0 "AP rebooted by controller user"
TRAP,
            'AP at site {{ location }} rebooted with reason AP rebooted by controller user',
            'Could not handle ruckusSZAPRebootTrap',
            [Severity::Error],
        );
    }
}
