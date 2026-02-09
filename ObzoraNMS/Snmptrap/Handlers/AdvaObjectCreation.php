<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class AdvaObjectCreation implements SnmptrapHandler
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
            $trap->log("User object $UserName created");
        } elseif ($trap_oid = $trap->findOid('F3-LAG-MIB::f3LagName')) {
            $lagID = substr($trap_oid, -1);
            $trap->log("LAG $lagID created");
        }
    }
}
