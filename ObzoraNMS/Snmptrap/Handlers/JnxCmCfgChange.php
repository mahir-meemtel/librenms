<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class JnxCmCfgChange implements SnmptrapHandler
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
        $source = $trap->getOidData($trap->findOid('JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventSource'));
        $user = $trap->getOidData($trap->findOid('JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventUser'));
        $changeTime = $trap->getOidData($trap->findOid('JUNIPER-CFGMGMT-MIB::jnxCmCfgChgEventDate'));
        if ($source == 'other' && $user == 'root') {
            $trap->log("Config rolled back at $changeTime");
        } else {
            $trap->log("Config modified by $user from $source at $changeTime");
        }
    }
}
