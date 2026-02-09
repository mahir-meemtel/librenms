<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class OspfNbrStateChange implements SnmptrapHandler
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
        $ospfNbrIpAddr = $trap->getOidData($trap->findOid('OSPF-MIB::ospfNbrRtrId'));
        $ospfNbr = $device->ospfNbrs()->where('ospfNbrRtrId', $ospfNbrIpAddr)->first();

        $ospfNbr->ospfNbrState = $trap->getOidData($trap->findOid('OSPF-MIB::ospfNbrState'));

        $severity = match ($ospfNbr->ospfNbrState) {
            'full' => Severity::Ok,
            'down' => Severity::Error,
            default => Severity::Warning,
        };

        $trap->log("OSPF neighbor $ospfNbrIpAddr changed state to $ospfNbr->ospfNbrState", $severity);

        $ospfNbr->save();
    }
}
