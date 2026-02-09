<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos6StackMgrRoleChange implements SnmptrapHandler
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
        $p_nr = $trap->getOidData($trap->findOid('ALCATEL-IND1-STACK-MANAGER-MIB::alaStackMgrPrimary'));
        $s_nr = $trap->getOidData($trap->findOid('ALCATEL-IND1-STACK-MANAGER-MIB::alaStackMgrSecondary'));
        $trap->log("Stack management change.Primary unit of the stack is now chassis: $p_nr. Secondary unit of the stack is now chassis: $s_nr.");
    }
}
