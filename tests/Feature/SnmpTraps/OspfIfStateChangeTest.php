<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\OspfPort;
use App\Models\Port;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class OspfIfStateChangeTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    //Test OSPF interface state down
    public function testOspfIfDown(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'designatedRouter']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 down
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240",
            "OSPF interface $port->ifName is down",
            'Could not handle ospfIfStateChange down',
            [Severity::Error],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'down');
    }

    //Test OSPF interface state DesignatedRouter
    public function testOspfIfDr(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'down']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 designatedRouter
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240
",
            "OSPF interface $port->ifName is designatedRouter",
            'Could not handle ospfIfStateChange designatedRouter',
            [Severity::Ok],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'designatedRouter');
    }

    //Test OSPF interface state backupDesignatedRouter
    public function testOspfIfBdr(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'down']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 backupDesignatedRouter
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240",
            "OSPF interface $port->ifName is backupDesignatedRouter",
            'Could not handle ospfIfStateChange backupDesignatedRouter',
            [Severity::Ok],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'backupDesignatedRouter');
    }

    //Test OSPF interface state otherDesignatedRouter
    public function testOspfIfOdr(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'down']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 otherDesignatedRouter
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240",
            "OSPF interface $port->ifName is otherDesignatedRouter",
            'Could not handle ospfIfStateChange otherDesignatedRouter',
            [Severity::Ok],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'otherDesignatedRouter');
    }

    //Test OSPF interface state pointToPoint
    public function testOspfIfPtp(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'down']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 pointToPoint
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240",
            "OSPF interface $port->ifName is pointToPoint",
            'Could not handle ospfIfStateChange pointToPoint',
            [Severity::Ok],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'pointToPoint');
    }

    //Test OSPF interface state waiting
    public function testOspfIfWait(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'designatedRouter']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 waiting
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240",
            "OSPF interface $port->ifName is waiting",
            'Could not handle ospfIfStateChange waiting',
            [Severity::Warning],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'waiting');
    }

    //Test OSPF interface state loopback
    public function testOspfIfLoop(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $port = Port::factory()->make(['ifAdminStatus' => 'up', 'ifOperStatus' => 'up']); /** @var Port $port */
        $device->ports()->save($port);

        $ospfIf = OspfPort::factory()->make(['port_id' => $port->port_id, 'ospfIfState' => 'designatedRouter']); /** @var OspfPort $ospfIf */
        $device->ospfPorts()->save($ospfIf);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:6:11:31.55
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfIfStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfIfIpAddress.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfIfIpAddress
OSPF-MIB::ospfAddressLessIf.$ospfIf->ospfIfIpAddress.0 $ospfIf->ospfAddressLessIf
OSPF-MIB::ospfIfState.$ospfIf->ospfIfIpAddress.0 loopback
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240",
            "OSPF interface $port->ifName is loopback",
            'Could not handle ospfIfStateChange loopback',
            [Severity::Warning],
            $device,
        );

        $ospfIf = $ospfIf->fresh();
        $this->assertEquals($ospfIf->ospfIfState, 'loopback');
    }
}
