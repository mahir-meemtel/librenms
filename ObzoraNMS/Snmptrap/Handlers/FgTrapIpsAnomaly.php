<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class FgTrapIpsAnomaly implements SnmptrapHandler
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
        $proto = $trap->getOidData($trap->findOid('FORTINET-FORTIGATE-MIB::fgIpsTrapSigMsg'));
        $trap->log("DDoS prevention triggered. Source: $srcIp Protocol: $proto", Severity::Warning);
    }
}
