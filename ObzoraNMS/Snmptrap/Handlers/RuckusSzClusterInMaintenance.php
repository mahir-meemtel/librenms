<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class RuckusSzClusterInMaintenance implements SnmptrapHandler
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
        $clusterName = $trap->getOidData($trap->findOid('RUCKUS-SZ-EVENT-MIB::ruckusSZClusterName'));
        $trap->log("Smartzone cluster $clusterName state changed to maintenance", Severity::Notice);
    }
}
