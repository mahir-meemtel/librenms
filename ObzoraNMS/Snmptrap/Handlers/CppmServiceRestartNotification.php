<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CppmServiceRestartNotification implements SnmptrapHandler
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
        $cppmServiceName = $trap->getOidData($trap->findOid('CPPM-MIB::cppmServiceName.0'));
        $cppmTrapMessage = $trap->getOidData($trap->findOid('CPPM-MIB::cppmTrapMessage.0'));
        $trap->log('Clearpass Service Trap - Host:' . $device->displayName() . ' Service:' . $cppmServiceName . ' Message:' . $cppmTrapMessage, Severity::Warning);
    }
}
