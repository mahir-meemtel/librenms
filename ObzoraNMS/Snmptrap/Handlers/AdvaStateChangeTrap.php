<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AdvaStateChangeTrap implements SnmptrapHandler
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
        if ($trap_oid = $trap->findOid('CM-FACILITY-MIB::cmEthernetAccPortAdminState')) {
            $adminState = $trap->getOidData($trap_oid);
            $opState = $trap->getOidData($trap->findOid('CM-FACILITY-MIB::cmEthernetAccPortOperationalState'));
            $portName = $trap->getOidData($trap->findOid('IF-MIB::ifName'));
            $trap->log("Port state change: $portName Admin State: $adminState Operational State: $opState");
        } elseif ($trap_oid = $trap->findOid('CM-FACILITY-MIB::cmFlowAdminState')) {
            $adminState = $trap->getOidData($trap_oid);
            $opState = $trap->getOidData($trap->findOid('CM-FACILITY-MIB::cmFlowOperationalState'));
            $flowID = substr($trap->findOid('CM-FACILITY-MIB::cmFlowAdminState'), 34);
            $flowID = str_replace('.', '-', $flowID);
            $trap->log("Flow state change: $flowID Admin State: $adminState Operational State: $opState");
        } elseif ($trap_oid = $trap->findOid('CM-FACILITY-MIB::cmEthernetNetPortAdminState')) {
            $adminState = $trap->getOidData($trap_oid);
            $opState = $trap->getOidData($trap->findOid('CM-FACILITY-MIB::cmEthernetNetPortOperationalState'));
            $portName = $trap->getOidData($trap->findOid('IF-MIB::ifName'));
            $trap->log("Port state change: $portName Admin State: $adminState Operational State: $opState");
        }
    }
}
