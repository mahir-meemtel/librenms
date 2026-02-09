<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Ipv4Address;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Tests\Traits\RequiresDatabase;

class FgTrapVpnTunTest extends SnmpTrapTestCase
{
    use RequiresDatabase;
    use DatabaseTransactions;

    public function testVpnTunDown(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ipv4 = Ipv4Address::factory()->make(); /** @var Ipv4Address $ipv4 */
        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 302:12:56:24.81
SNMPv2-MIB::snmpTrapOID.0 FORTINET-FORTIGATE-MIB::fgTrapVpnTunDown
FORTINET-CORE-MIB::fnSysSerial.0 $device->serial
SNMPv2-MIB::sysName.0 $device->hostname
FORTINET-FORTIGATE-MIB::fgVpnTrapLocalGateway.0 $device->ip
FORTINET-FORTIGATE-MIB::fgVpnTrapRemoteGateway.0 $ipv4->ipv4_address
FORTINET-FORTIGATE-MIB::fgVpnTrapPhase1Name.0 test_tunnel_down",
            "VPN tunnel test_tunnel_down to $ipv4->ipv4_address is down",
            'Could not handle fgTrapVpnTunDown',
            [Severity::Notice],
            $device,
        );
    }

    public function testVpnTunUp(): void
    {
        $device = Device::factory()->create(); /** @var Device $device */
        $ipv4 = Ipv4Address::factory()->make(); /** @var Ipv4Address $ipv4 */
        $this->assertTrapLogsMessage("$device->hostname
UDP: [$device->ip]:57602->[192.168.5.5]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 302:12:56:24.81
SNMPv2-MIB::snmpTrapOID.0 FORTINET-FORTIGATE-MIB::fgTrapVpnTunUp
SNMPv2-MIB::sysName.0 $device->hostname
FORTINET-FORTIGATE-MIB::fgVpnTrapLocalGateway.0 $device->ip
FORTINET-FORTIGATE-MIB::fgVpnTrapRemoteGateway.0 $ipv4->ipv4_address
FORTINET-FORTIGATE-MIB::fgVpnTrapPhase1Name.0 test_tunnel_up",
            "VPN tunnel test_tunnel_up to $ipv4->ipv4_address is up",
            'Could not handle fgTrapVpnTunUp',
            [Severity::Ok],
            $device,
        );
    }
}
