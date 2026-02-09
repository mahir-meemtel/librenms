<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Mgnt2TrapNmsEvent implements SnmptrapHandler
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
        $eventObj = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogObjectClassIdentifier'));
        $sourcePm = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogSourcePm'));
        $slot = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogBoardNumber'));
        $portType = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogSourcePortType'));
        $portNum = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogSourcePortNumber'));
        $logReason = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogReason'));
        $logAdd = $trap->getOidData($trap->findOid('EKINOPS-MGNT2-NMS-MIB::mgnt2EventLogAdditionalText'));

        // Adding additional info if it exists.
        if (! empty($logAdd)) {
            $logReason = "$logReason Additional info: $logAdd";
        }

        if ($eventObj == 'port') {
            $msg = "Event on slot $slot, $sourcePm Port: $portType $portNum. Reason: $logReason";
        } else {
            $msg = "Event on slot $slot, $sourcePm Reason: $logReason";
        }
        $trap->log($msg);
    }
}
