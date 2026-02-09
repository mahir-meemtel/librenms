<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class JnxDomLaneAlarmCleared implements SnmptrapHandler
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
        $currentAlarm = $trap->getOidData($trap->findOid('JUNIPER-DOM-MIB::jnxDomCurrentLaneAlarms'));
        $lane = $trap->getOidData($trap->findOid('JUNIPER-DOM-MIB::jnxDomLaneIndex'));
        $alarmList = JnxDomLaneAlarmId::getLaneAlarms($currentAlarm);

        $ifIndex = substr(strrchr($trap->findOid('IF-MIB::ifDescr'), '.'), 1);
        $port = $device->ports()->where('ifIndex', $ifIndex)->first();
        if (! $port) {
            Log::warning("Snmptrap JnxDomLaneAlarmCleared: Could not find port at ifIndex $ifIndex for device: " . $device->hostname);

            return;
        }

        $trap->log("DOM lane alarm cleared on interface $port->ifDescr lane $lane. Current alarm(s): $alarmList", Severity::Ok);
    }
}
