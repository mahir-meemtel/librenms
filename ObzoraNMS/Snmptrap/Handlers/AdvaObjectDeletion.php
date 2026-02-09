<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AdvaObjectDeletion implements SnmptrapHandler
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
        if ($trap_oid = $trap->findOid('CM-SECURITY-MIB::cmSecurityUserName')) {
            $UserName = $trap->getOidData($trap_oid);
            $trap->log("User object $UserName deleted");
        } elseif ($trap_oid = $trap->findOid('CM-FACILITY-MIB::cmFlowIndex')) {
            $flowID = str_replace('.', '-', substr($trap_oid, 29));
            $trap->log("Flow $flowID deleted");
        } elseif ($trap_oid = $trap->findOid('F3-LAG-MIB::f3LagPortIndex')) {
            $lagPortID = $trap->getOidData($trap_oid);
            $lagID = str_replace('.', '-', substr($trap_oid, -5, 3));
            $trap->log("LAG member port $lagPortID removed from LAG $lagID");
        } elseif ($trap_oid = $trap->findOid('F3-LAG-MIB::f3LagIndex')) {
            $lagID = $trap->getOidData($trap_oid);
            $trap->log("LAG $lagID deleted");
        }
    }
}
