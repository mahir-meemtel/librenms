<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CienaCesPortNotificationPortDown implements SnmptrapHandler
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
        $chassis = CienaCesPortNotificationUtils::getCienaChassis($trap);
        $shelf = CienaCesPortNotificationUtils::getCienaShelf($trap);
        $slot = CienaCesPortNotificationUtils::getCienaSlot($trap);
        $port = CienaCesPortNotificationUtils::getCienaPort($trap);
        $trap->log("Port down on Chassis: $chassis Shelf: $shelf Slot: $slot Port: $port", Severity::Error);

        $librePort = $device->ports()->where('ifIndex', $port)->first();
        $librePort->ifOperStatus = 'down';
        $librePort->save();
    }
}
