<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class OspfTxRetransmit implements SnmptrapHandler
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
        $ospfRouterId = $trap->getOidData($trap->findOid('OSPF-MIB::ospfRouterId'));
        $ospfPacketType = $trap->getOidData($trap->findOid('OSPF-TRAP-MIB::ospfPacketType'));
        $ospfLsdbRouterId = $trap->getOidData($trap->findOid('OSPF-MIB::ospfLsdbRouterId'));
        $ospfLsdbType = $trap->getOidData($trap->findOid('OSPF-MIB::ospfLsdbType'));
        $ospfNbrRtrId = $trap->getOidData($trap->findOid('OSPF-MIB::ospfNbrRtrId'));
        $ospfLsdbLsid = $trap->getOidData($trap->findOid('OSPF-MIB::ospfLsdbLsid'));

        if ($ospfPacketType != 'lsUpdate') {
            $trap->log('SNMP TRAP: ' . $device->displayName() . "(Router ID: $ospfRouterId) sent a $ospfPacketType packet to $ospfNbrRtrId.");

            return;
        }

        $trap->log('SNMP Trap: OSPFTxRetransmit trap received from ' . $device->displayName() . "(Router ID: $ospfRouterId). A $ospfPacketType packet was sent to $ospfNbrRtrId. LSType: $ospfLsdbType, route ID: $ospfLsdbLsid, originating from $ospfLsdbRouterId.");
    }
}
