<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

class OspfTxRetransmitTest extends SnmpTrapTestCase
{
    /**
     * Test OSPF lsUpdate packet type trap
     *
     * @return void
     */
    public function testLsUpdatePacket(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 16:21:49.33
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfTxRetransmit
OSPF-MIB::ospfRouterId 10.1.2.3
OSPF-MIB::ospfIfIpAddress 10.8.9.10
OSPF-MIB::ospfAddressLessIf 0
OSPF-MIB::ospfNbrRtrId 10.3.4.5
OSPF-TRAP-MIB::ospfPacketType lsUpdate
OSPF-MIB::ospfLsdbType routerLink
OSPF-MIB::ospfLsdbLsid 10.1.1.0
OSPF-MIB::ospfLsdbRouterId 10.4.5.6
TRAP,
            'SNMP Trap: OSPFTxRetransmit trap received from {{ hostname }}(Router ID: 10.1.2.3). A lsUpdate packet was sent to 10.3.4.5. LSType: routerLink, route ID: 10.1.1.0, originating from 10.4.5.6.',
            'Could not handle testlsUpdatePacket trap',
        );
    }

    /**
     * Test OSPF non lsUpdate packet type
     *
     * @return void
     */
    public function testNotLsUpdatePacket(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[10.0.0.1]:162
SNMPv2-MIB::sysUpTime.0 16:21:49.33
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfTxRetransmit
OSPF-MIB::ospfRouterId 10.1.2.3
OSPF-MIB::ospfIfIpAddress 10.8.9.10
OSPF-MIB::ospfAddressLessIf 0
OSPF-MIB::ospfNbrRtrId 10.3.4.5
OSPF-TRAP-MIB::ospfPacketType hello
OSPF-MIB::ospfLsdbType routerLink
OSPF-MIB::ospfLsdbLsid 10.1.1.0
OSPF-MIB::ospfLsdbRouterId 10.4.5.6
TRAP,
            'SNMP TRAP: {{ hostname }}(Router ID: 10.1.2.3) sent a hello packet to 10.3.4.5.',
            'Could not handle testNotLsUpdatePacket trap',
        );
    }
}
