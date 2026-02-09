<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\OspfNbr;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class OspfNbrStateChangeTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    //Test OSPF neighbor state down trap
    public function testOspfNbrDown(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ospfNbr = OspfNbr::factory()->make(['ospfNbrState' => 'full']); /** @var OspfNbr $ospfNbr */
        $ospfNbr->ospf_nbr_id = "$ospfNbr->ospfNbrIpAddr.$ospfNbr->ospfNbrAddressLessIndex";
        $device->ospfNbrs()->save($ospfNbr);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:1:07:16.06
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfNbrStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfNbrIpAddr.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrIpAddr
OSPF-MIB::ospfNbrAddressLessIndex.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrAddressLessIndex
OSPF-MIB::ospfNbrRtrId.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrRtrId
OSPF-MIB::ospfNbrState.$ospfNbr->ospf_nbr_id down
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240 ",
            "OSPF neighbor $ospfNbr->ospfNbrRtrId changed state to down",
            'Could not handle ospfNbrStateChange down',
            [Severity::Error],
            $device,
        );

        $ospfNbr = $ospfNbr->fresh();
        $this->assertEquals($ospfNbr->ospfNbrState, 'down');
    }

    //Test OSPF neighbor state full trap
    public function testOspfNbrFull(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ospfNbr = OspfNbr::factory()->make(['ospfNbrState' => 'down']); /** @var OspfNbr $ospfNbr */
        $ospfNbr->ospf_nbr_id = "$ospfNbr->ospfNbrIpAddr.$ospfNbr->ospfNbrAddressLessIndex";
        $device->ospfNbrs()->save($ospfNbr);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:1:07:16.06
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfNbrStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfNbrIpAddr.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrIpAddr
OSPF-MIB::ospfNbrAddressLessIndex.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrAddressLessIndex
OSPF-MIB::ospfNbrRtrId.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrRtrId
OSPF-MIB::ospfNbrState.$ospfNbr->ospf_nbr_id full
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240 ",
            "OSPF neighbor $ospfNbr->ospfNbrRtrId changed state to full",
            'Could not handle ospfNbrStateChange full',
            [Severity::Ok],
            $device,
        );

        $ospfNbr = $ospfNbr->fresh();
        $this->assertEquals($ospfNbr->ospfNbrState, 'full');
    }

    //Test OSPF neighbor state trap any other state
    public function testOspfNbrOther(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ospfNbr = OspfNbr::factory()->make(['ospfNbrState' => 'full']); /** @var OspfNbr $ospfNbr */
        $ospfNbr->ospf_nbr_id = "$ospfNbr->ospfNbrIpAddr.$ospfNbr->ospfNbrAddressLessIndex";
        $device->ospfNbrs()->save($ospfNbr);

        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 0:1:07:16.06
SNMPv2-MIB::snmpTrapOID.0 OSPF-TRAP-MIB::ospfNbrStateChange
OSPF-MIB::ospfRouterId.0 $device->ip
OSPF-MIB::ospfNbrIpAddr.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrIpAddr
OSPF-MIB::ospfNbrAddressLessIndex.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrAddressLessIndex
OSPF-MIB::ospfNbrRtrId.$ospfNbr->ospf_nbr_id $ospfNbr->ospfNbrRtrId
OSPF-MIB::ospfNbrState.$ospfNbr->ospf_nbr_id exstart
SNMPv2-MIB::snmpTrapEnterprise.0 JUNIPER-CHASSIS-DEFINES-MIB::jnxProductNameSRX240 ",
            "OSPF neighbor $ospfNbr->ospfNbrRtrId changed state to exstart",
            'Could not handle ospfNbrStateChange exstart',
            [Severity::Warning],
            $device,
        );

        $ospfNbr = $ospfNbr->fresh();
        $this->assertEquals($ospfNbr->ospfNbrState, 'exstart');
    }
}
