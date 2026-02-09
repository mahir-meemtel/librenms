<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

class JnxCmCfgChangeTest extends SnmpTrapTestCase
{
    public function testConfigChangeTrap(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-CFGMGMT-MIB::jnxCmCfgChange
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventTime.54 316:13:26:37.65
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventDate.54 2018-11-21,7:34:39.0,-6:0
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventSource.54 cli
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventUser.54 TestUser
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameEX2200
TRAP,
            'Config modified by TestUser from cli at 2018-11-21,7:34:39.0,-6:0',
            'Could not handle JnxCmCfgChange trap',
        );
    }

    public function testConfigRollbackTrap(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:64610->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 198:2:10:48.91
SNMPv2-MIB::snmpTrapOID.0 JUNIPER-CFGMGMT-MIB::jnxCmCfgChange
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventTime.54 316:13:26:37.65
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventDate.54 2017-12-21,7:34:39.0,-6:0
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventSource.54 other
JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventUser.54 root
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameEX2200
TRAP,
            'Config rolled back at 2017-12-21,7:34:39.0,-6:0',
            'Could not handle JnxCmCfgChange config rolled back',
        );
    }
}
