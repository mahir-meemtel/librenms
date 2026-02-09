<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos6HicServerTrap implements SnmptrapHandler
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
        $ip = $trap->getOidData($trap->findOid('ALCATEL-IND1-AAA-MIB::aaaHSvrIpAddress'));
        $trap->log("Radius server with the IP: $ip might be unreachable or recovered.");
    }
}
