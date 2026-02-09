<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;

class VmwHBTest extends SnmpTrapTestCase
{
    public function testVmwVmHBLostTrap(): void
    {
        $guest = Device::factory()->make(); /** @var Device $guest */
        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:28386->[10.10.10.100]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 5:18:30:26.00
SNMPv2-MIB::snmpTrapOID.0 VMWARE-VMINFO-MIB::vmwVmHBLost
VMWARE-VMINFO-MIB::vmwVmID.0 28 VMWARE-VMINFO-MIB::vmwVmConfigFilePath.0 /vmfs/volumes/50101bda-eaf6ac7e-7e44-d4ae5267fb9f/$guest->hostname/$guest->hostname.vmx
VMWARE-VMINFO-MIB::vmwVmDisplayName.28 $guest->hostname
SNMP-COMMUNITY-MIB::snmpTrapAddress.0 $guest->ip
SNMP-COMMUNITY-MIB::snmpTrapCommunity.0 \"public\"
SNMPv2-MIB::snmpTrapEnterprise.0 VMWARE-PRODUCTS-MIB::vmwESX",
            "Heartbeat from guest $guest->hostname lost",
            'Could not handle VmwVmHBLostTrap',
            [Severity::Warning],
        );
    }

    public function testVmwVmHBDetectedTrap(): void
    {
        $guest = Device::factory()->make(); /** @var Device $guest */
        $this->assertTrapLogsMessage("{{ hostname }}
UDP: [{{ ip }}]:28386->[10.10.10.100]:162
DISMAN-EVENT-MIB::sysUpTimeInstance 5:18:30:26.00
SNMPv2-MIB::snmpTrapOID.0 VMWARE-VMINFO-MIB::vmwVmHBDetected
VMWARE-VMINFO-MIB::vmwVmID.0 28 VMWARE-VMINFO-MIB::vmwVmConfigFilePath.0 /vmfs/volumes/50101bda-eaf6ac7e-7e44-d4ae5267fb9f/$guest->hostname/$guest->hostname.vmx
VMWARE-VMINFO-MIB::vmwVmDisplayName.28 $guest->hostname
SNMP-COMMUNITY-MIB::snmpTrapAddress.0 $guest->ip
SNMP-COMMUNITY-MIB::snmpTrapCommunity.0 \"public\"
SNMPv2-MIB::snmpTrapEnterprise.0 VMWARE-PRODUCTS-MIB::vmwESX",
            "Heartbeat from guest $guest->hostname detected",
            'Could not handle VmwVmHBDetectedTrap',
            [Severity::Ok],
        );
    }
}
