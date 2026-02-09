<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusAssocTrap implements SnmptrapHandler
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
        $macRaw = $trap->getOidData($trap->findOid('RUCKUS-EVENT-MIB::ruckusEventClientMacAddr'));
        $mac = substr($macRaw, 0, 17);
        $trap->log("Client $mac associated");
    }
}
