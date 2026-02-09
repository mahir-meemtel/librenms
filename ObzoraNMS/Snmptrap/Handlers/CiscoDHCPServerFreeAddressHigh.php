<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CiscoDHCPServerFreeAddressHigh implements SnmptrapHandler
{
    /**
     * Handle snmptrap.
     * Data is pre-parsed and delivered as a Trap.
     *
     * @param  Device  $device
     * @param  Trap  $trap
     * @return void
     */
    public function handle(Device $device, Trap $trap)
    {
        $oid_prefix = 'CISCO-IETF-DHCP-SERVER-MIB::cDhcpv4ServerSharedNetFreeAddresses.';
        $oid = $trap->findOid($oid_prefix);
        $pool = str_replace($oid_prefix, '', $oid);
        $value = $trap->getOidData($oid);
        $trap->log("SNMP Trap: DHCP pool $pool address space high. Free addresses: '$value' addresses.", Severity::Info);
    }
}
