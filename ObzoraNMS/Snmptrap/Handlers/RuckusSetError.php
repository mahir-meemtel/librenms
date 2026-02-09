<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusSetError implements SnmptrapHandler
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
        $errorOidDirty = $trap->getOidData($trap->findOid('RUCKUS-EVENT-MIB::ruckusEventSetErrorOID'));
        $errorOid = substr($errorOidDirty, 43);
        $trap->log("SNMP set error on oid $errorOid");
    }
}
