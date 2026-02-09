<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos7portViolationNotification implements SnmptrapHandler
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
        $reason = $trap->getOidData($trap->findOid('ALCATEL-IND1-PORT-MIB::portViolationRecoveryReason.1.0'));
        $ifIndex = $trap->getOidData($trap->findOid('IF-MIB::ifIndex'));
        $port = $device->ports()->where('ifIndex', $ifIndex)->first();
        $trap->log("There has been a loop detected on the port $port->ifDescr. The current status code is: $reason.", Severity::Error);
    }
}
