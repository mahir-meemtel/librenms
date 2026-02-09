<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos7portViolation implements SnmptrapHandler
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
        $reason = $trap->getOidData($trap->findOid('ALCATEL-IND1-PORT-MIB::portViolationSource.2.0'));
        $current = $trap->getOidData($trap->findOid('ALCATEL-IND1-PORT-MIB::portViolationReason.3.0'));
        $ifIndex = $trap->getOidData($trap->findOid('IF-MIB::ifIndex'));
        $port = $device->ports()->where('ifIndex', $ifIndex)->first();
        $trap->log("There has been a loop detected on the port $port->ifDescr. The source code of the violation is: $reason and the current status code is: $current.", Severity::Error);
    }
}
