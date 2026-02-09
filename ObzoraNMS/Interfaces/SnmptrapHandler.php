<?php
namespace ObzoraNMS\Interfaces;

use App\Models\Device;
use ObzoraNMS\Snmptrap\Trap;

interface SnmptrapHandler
{
    /**
     * Handle snmptrap.
     * Data is pre-parsed and delivered as a Trap.
     *
     * @param  Device  $device
     * @param  Trap  $trap
     * @return void
     */
    public function handle(Device $device, Trap $trap);
}
