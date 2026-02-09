<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CiscoNSRejectRegNotify implements SnmptrapHandler
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
        $Code = $trap->getOidData($trap->findOid('CISCO-NS-MIB::fcNameServerRejectReasonCode'));
        $Exp = $trap->getOidData($trap->findOid('CISCO-NS-MIB::fcNameServerRejReasonCodeExp'));

        $trap->log("Cisco Nameserver rejected a registration request with error code $Code due to $Exp", Severity::Warning);
    }
}
