<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxPowerSupplyFailure implements SnmptrapHandler
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
        $powerSupply = $trap->getOidData($trap->findOid('JUNIPER-MIB::jnxContentsDescr'));
        $state = $trap->getOidData($trap->findOid('JUNIPER-MIB::jnxOperatingState'));
        $trap->log("Power Supply $powerSupply is $state", Severity::Error);
    }
}
