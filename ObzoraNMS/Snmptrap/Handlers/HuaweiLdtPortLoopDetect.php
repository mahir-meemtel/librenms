<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class HuaweiLdtPortLoopDetect implements SnmptrapHandler
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
        $trap->log('Loop Detected ' . $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwLPortLoopDetectIfName')) . ' (Status ' . $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwPortLoopDetectStatus')) . ', possible VLANs ' . $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwLdtPortLoopDetectVlanList')) . ', auto VLANs ' . $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwLdtPortLoopAutoTrapVlanList')) . ')', Severity::Warning, 'loop', $trap->getOidData($trap->findOid('HUAWEI-LDT-MIB::hwLPortLoopDetectIfName')));
    }
}
