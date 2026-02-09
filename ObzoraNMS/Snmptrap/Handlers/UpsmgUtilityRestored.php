<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class UpsmgUtilityRestored implements SnmptrapHandler
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
        $sensor = $device->sensors()->where('sensor_type', 'upsmgInputBadStatus')->first();
        if (! $sensor) {
            Log::warning('Snmptrap UpsmgUtilityRestored: Could not find matching sensor upsmgInputBadStatus for device: ' . $device->hostname);

            return;
        }
        $sensor->sensor_current = 2;
        $sensor->save();
        $trap->log('UPS power restored, state sensor ' . $sensor->sensor_descr . ' has changed to ' . $sensor->sensor_current . '.', Severity::Ok, 'Power');
    }
}
