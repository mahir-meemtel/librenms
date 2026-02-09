<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class CiscoLdpSesDown implements SnmptrapHandler
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
        $ifIndex = $trap->getOidData($trap->findOid('IF-MIB::ifIndex'));
        $port = $device->ports()->where('ifIndex', $ifIndex)->first();
        /*
        if (! $port) {
            $trap->log("Snmptrap ciscoLdpSesDown: Could not find port at ifIndex $ifIndex for device: $device->hostname", Severity::Warning);
            Log::warning("Snmptrap ciscoLdpSesDown: Could not find port at ifIndex $ifIndex for device: " . $device->hostname);

            return;
        }
        */
        $severity = Severity::Warning;
        $trap->log("LDP session DOWN on interface $port->ifDescr - $port->ifAlias", $severity, 'interface', $port->port_id);
    }
}
