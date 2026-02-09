<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class JnxLdpSesUp implements SnmptrapHandler
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
        $state = $trap->getOidData($trap->findOid('JUNIPER-MPLS-LDP-MIB::jnxMplsLdpSesState'));
        $ifIndex = $trap->getOidData($trap->findOid('JUNIPER-LDP-MIB::jnxLdpSesUpIf'));
        $port = $device->ports()->where('ifIndex', $ifIndex)->first();

        if (! $port) {
            Log::warning("Snmptrap LdpSesUp: Could not find port at ifIndex $ifIndex for device: " . $device->hostname);

            return;
        }

        $trap->log("LDP session on interface $port->ifDescr is $state", Severity::Ok);
    }
}
