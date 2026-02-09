<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class PoseidonSensAlarmStateChanged implements SnmptrapHandler
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
        $oid = $trap->findOid('POSEIDON-MIB::sensName');
        $id = substr($oid, strlen($oid) + 1);

        $SensorName = $trap->getOidData($trap->findOid('POSEIDON-MIB::sensName.' . $id));
        $SensorState = $trap->getOidData($trap->findOid('POSEIDON-MIB::sensState.' . $id));
        $RawSensorValue = $trap->getOidData($trap->findOid('POSEIDON-MIB::sensValue.' . $id));
        $SensorUnit = $trap->getOidData($trap->findOid('POSEIDON-MIB::sensUnit.' . $id));
        $SensorValue = (int) $RawSensorValue / 10;

        // Match Poseidon sensor states to ObzoraNMS eventlog colours
        switch ($SensorState) {
            case 'invalid':
                $State = 'invalid';
                $SeverityColour = Severity::Warning; // yellow
                break;
            case 'normal':
                $State = 'normal';
                $SeverityColour = Severity::Ok; // green
                break;
            case 'alarmstate':
                $State = 'alarmstate';
                $SeverityColour = Severity::Error; // red
                break;
            case 'alarm':
                $State = 'alarm';
                $SeverityColour = Severity::Error; // red
                break;
            default:
                $State = 'unknown';
                $SeverityColour = Severity::Warning; // yellow
        }
        $trap->log("Poseidon Sensor State Change: $SensorName changed state to $State: $SensorValue $SensorUnit", $SeverityColour);
    }
}
