<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\PowerState;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class VmwVmPoweredOff implements SnmptrapHandler
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

        /** @var \App\Models\Vminfo $vminfo */
        $vminfo = $device->vminfo()->where('vmwVmDisplayName', $vmGuestName)->first();
        $vminfo->vmwVmState = PowerState::OFF;
        $vminfo->save();

        $trap->log("Guest $vmGuestName was powered off");
    }
}
