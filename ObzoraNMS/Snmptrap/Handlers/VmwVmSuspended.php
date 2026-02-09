<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\PowerState;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class VmwVmSuspended implements SnmptrapHandler
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
        $vmGuestName = VmwTrapUtil::getGuestName($trap);

        $vminfo = $device->vminfo()->where('vmwVmDisplayName', $vmGuestName)->first();
        $vminfo->vmwVmState = PowerState::SUSPENDED;

        $trap->log("Guest $vmGuestName has been suspended");

        $vminfo->save();
    }
}
