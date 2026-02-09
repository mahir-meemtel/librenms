<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class LinkDown implements SnmptrapHandler
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
            Log::warning("Snmptrap linkDown: Could not find port at ifIndex $ifIndex for device: " . $device->hostname);

            return;
        }

        $port->ifOperStatus = 'down';

        $trapAdminStatus = $trap->getOidData("IF-MIB::ifAdminStatus.$ifIndex");
        if ($trapAdminStatus) {
            $port->ifAdminStatus = $trapAdminStatus;
        }
        $trap->log("SNMP Trap: linkDown $port->ifAdminStatus/$port->ifOperStatus " . $port->ifDescr, Severity::Error, 'interface', $port->port_id);

        if ($port->isDirty('ifAdminStatus')) {
            $trap->log("Interface Disabled : $port->ifDescr (TRAP)", Severity::Notice, 'interface', $port->port_id);
        }

        if ($port->isDirty('ifOperStatus')) {
            $trap->log("Interface went Down : $port->ifDescr (TRAP)", Severity::Error, 'interface', $port->port_id);
        }

        $port->save();
    }
}
