<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos6LbdStateChangeToShutdown implements SnmptrapHandler
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
        $before = $trap->getOidData($trap->findOid('ALCATEL-IND1-LBD-MIB::alaLbdPreviousState'));
        $current = $trap->getOidData($trap->findOid('ALCATEL-IND1-LBD-MIB::alaLbdCurrentState'));
        $ifIndex = $trap->getOidData($trap->findOid('ALCATEL-IND1-LBD-MIB::alaLbdPortIfIndex'));
        $port = $device->ports()->where('ifIndex', $ifIndex)->first();
        $trap->log("There has been a loop detected on the port $port->ifDescr. Status of the port before was $before and now is $current.", Severity::Error);
    }
}
