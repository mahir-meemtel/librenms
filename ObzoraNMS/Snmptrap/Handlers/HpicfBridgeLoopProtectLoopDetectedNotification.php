<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class HpicfBridgeLoopProtectLoopDetectedNotification implements SnmptrapHandler
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
        $ifIndex = $trap->getOidData($trap->findOid('IF-MIB::ifIndex'));

        $port = $device->ports()->where('ifIndex', $ifIndex)->first();

        $interface = $ifIndex . ' (ifIndex)';
        if ($port) {
            $interface = $port->ifDescr;
        } else {
            Log::warning("SnmpTrap HpicfBridgeLoopProtectLoopDetectedNotification: Could not find port at ifIndex $ifIndex for device: " . $device->hostname);
        }

        $trap->log('Loop Detected ' . $interface . ' (Count ' . $trap->getOidData($trap->findOid('HP-ICF-BRIDGE::hpicfBridgeLoopProtectPortLoopCount')) . ', Action ' . $trap->getOidData($trap->findOid('HP-ICF-BRIDGE::hpicfBridgeLoopProtectPortReceiverAction')) . ')', Severity::Warning, 'loop', $interface);
    }
}
