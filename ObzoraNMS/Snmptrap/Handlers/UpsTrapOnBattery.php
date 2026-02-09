<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use Illuminate\Support\Str;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class UpsTrapOnBattery implements SnmptrapHandler
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
        $min_remaining = (int) Str::before($trap->getOidData($trap->findOid('UPS-MIB::upsEstimatedMinutesRemaining')), ' ');
        $sec_time = (int) Str::before($trap->getOidData($trap->findOid('UPS-MIB::upsSecondsOnBattery')), ' ');
        $trap->log("UPS running on battery for $sec_time seconds. Estimated $min_remaining minutes remaining", Severity::Error);
        $sensor_remaining = $device->sensors()->where('sensor_index', '200')->where('sensor_type', 'rfc1628')->first();
        if (! $sensor_remaining) {
            Log::warning("Snmptrap upsTrapOnBattery: Could not find matching sensor \'Estimated battery time remaining\' for device: " . $device->hostname);

            return;
        }
        $sensor_remaining->sensor_current = $min_remaining / $sensor_remaining->sensor_divisor;
        $sensor_remaining->save();

        $sensor_time = $device->sensors()->where('sensor_index', '100')->where('sensor_type', 'rfc1628')->first();
        if (! $sensor_time) {
            Log::warning("Snmptrap upsTrapOnBattery: Could not find matching sensor \'Time on battery\' for device: " . $device->hostname);

            return;
        }
        $sensor_time->sensor_current = $sec_time / $sensor_time->sensor_divisor;
        $sensor_time->save();

        $sensor_output = $device->sensors()->where('sensor_type', 'upsOutputSourceState')->first();
        if (! $sensor_output) {
            Log::warning("Snmptrap upsTrapOnBattery: Could not find matching sensor \'upsOutputSourceState\' for device: " . $device->hostname);

            return;
        }
        $sensor_output->sensor_current = 5;
        $sensor_output->save();
    }
}
