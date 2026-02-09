<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos6CfgSavedTrap implements SnmptrapHandler
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
        $descr = $trap->getOidData($trap->findOid('ALCATEL-IND1-CONFIG-MGR-MIB::alcatelIND1ConfigMgrMIB.3.1.1'));
        $trap->log("$descr");
    }
}
