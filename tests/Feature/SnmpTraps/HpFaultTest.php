<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use ObzoraNMS\Enum\Severity;

class HpFaultTest extends SnmpTrapTestCase
{
    public function testBadCable(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:44298->[192.168.5.5]:162
SNMPv2-MIB::snmpTrapOID.0 HP-ICF-FAULT-FINDER-MIB::hpicfFaultFinderTrap
DISMAN-EVENT-MIB::sysUpTimeInstance 133:19:41:09.17
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogFaultType.1510 badCable
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogAction.1510 warn
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogSeverity.1510 medium
HP-ICF-FAULT-FINDER-MIB::hpicfFfFaultInfoURL.0.1510 http:\/\/{{ ip }}\/cgi\/fDetail?index=1510
SNMP-COMMUNITY-MIB::snmpTrapAddress.0 {{ ip }}
SNMP-COMMUNITY-MIB::snmpTrapCommunity.0 public
SNMPv2-MIB::snmpTrapEnterprise.0 HP-ICF-OID::hpicfCommonTraps
TRAP,
            "Fault - Bad Cable http:\/\/{{ ip }}\/cgi\/fDetail?index=1510",
            'Could not handle HP-ICF-FAULT-FINDER-MIB::hpicfFaultFinderTrap trap',
            [Severity::Warning, 'badCable'],
        );
    }

    public function testBadDriver(): void
    {
        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:44298->[192.168.5.5]:162
SNMPv2-MIB::snmpTrapOID.0 HP-ICF-FAULT-FINDER-MIB::hpicfFaultFinderTrap
DISMAN-EVENT-MIB::sysUpTimeInstance 133:19:41:09.17
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogFaultType.1510 badDriver
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogAction.1510 warn
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogSeverity.1510 medium
HP-ICF-FAULT-FINDER-MIB::hpicfFfFaultInfoURL.0.1510 http:\/\/{{ ip }}\/cgi\/fDetail?index=1510
SNMP-COMMUNITY-MIB::snmpTrapAddress.0 {{ ip }}
SNMP-COMMUNITY-MIB::snmpTrapCommunity.0 public
SNMPv2-MIB::snmpTrapEnterprise.0 HP-ICF-OID::hpicfCommonTraps",
            "Fault - Unhandled http:\/\/{{ ip }}\/cgi\/fDetail?index=1510",
            'Could not handle HP-ICF-FAULT-FINDER-MIB::hpicfFaultFinderTrap trap',
            [Severity::Info, 'badDriver'],
        );
    }

    public function testBcastStorm(): void
    {
        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:44298->[192.168.5.5]:162
SNMPv2-MIB::snmpTrapOID.0 HP-ICF-FAULT-FINDER-MIB::hpicfFaultFinderTrap
DISMAN-EVENT-MIB::sysUpTimeInstance 133:19:41:09.17
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogFaultType.1510 bcastStorm
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogAction.1510 warn
HP-ICF-FAULT-FINDER-MIB::hpicfFfLogSeverity.1510 medium
HP-ICF-FAULT-FINDER-MIB::hpicfFfFaultInfoURL.0.1510 http:\/\/{{ ip }}\/cgi\/fDetail?index=1510
SNMP-COMMUNITY-MIB::snmpTrapAddress.0 {{ ip }}
SNMP-COMMUNITY-MIB::snmpTrapCommunity.0 public
SNMPv2-MIB::snmpTrapEnterprise.0 HP-ICF-OID::hpicfCommonTraps",
            "Fault - Broadcaststorm http:\/\/{{ ip }}\/cgi\/fDetail?index=1510",
            'Could not handle HP-ICF-FAULT-FINDER-MIB::hpicfFaultFinderTrap trap',
            [Severity::Error, 'bcastStorm'],
        );
    }
}
