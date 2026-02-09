<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class LinkUp implements SnmptrapHandler
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

        if (! $port) {
            Log::warning("Snmptrap linkUp: Could not find port at ifIndex $ifIndex for device: " . $device->hostname);

            return;
        }

        $port->ifOperStatus = $trap->getOidData("IF-MIB::ifOperStatus.$ifIndex") ?: 'up';
        $port->ifAdminStatus = $trap->getOidData("IF-MIB::ifAdminStatus.$ifIndex") ?: 'up'; // If we receive LinkUp trap, we can safely assume that the ifAdminStatus is also up.

        $trap->log("SNMP Trap: linkUp $port->ifAdminStatus/$port->ifOperStatus " . $port->ifDescr, Severity::Ok, 'interface', $port->port_id);

        if ($port->isDirty('ifAdminStatus')) {
            $trap->log("Interface Enabled : $port->ifDescr (TRAP)", Severity::Notice, 'interface', $port->port_id);
        }

        if ($port->isDirty('ifOperStatus')) {
            $trap->log("Interface went Up : $port->ifDescr (TRAP)", Severity::Ok, 'interface', $port->port_id);
        }

        $port->save();
    }
}
