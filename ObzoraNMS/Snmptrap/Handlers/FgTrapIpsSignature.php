<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class FgTrapIpsSignature implements SnmptrapHandler
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
        $srcIp = $trap->getOidData($trap->findOid('FORTINET-FORTIGATE-MIB::fgIpsTrapSrcIp'));
        $sigNum = $trap->getOidData($trap->findOid('FORTINET-FORTIGATE-MIB::fgIpsTrapSigId'));
        $sigName = $trap->getOidData($trap->findOid('FORTINET-FORTIGATE-MIB::fgIpsTrapSigMsg'));
        $trap->log("IPS signature $sigName detected from $srcIp with Fortiguard ID $sigNum", Severity::Warning);
    }
}
