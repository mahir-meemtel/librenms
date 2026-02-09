<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class HuaweiLdtPortLoopDetectRecovery implements SnmptrapHandler
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
        $trap->log('Loop Detect Recovery ' . $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwLPortLoopDetectIfName')) . ' (Status ' . $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwPortLoopDetectStatus')) . ')', Severity::Ok, 'loop', $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwLPortLoopDetectIfName')));
    }
}
