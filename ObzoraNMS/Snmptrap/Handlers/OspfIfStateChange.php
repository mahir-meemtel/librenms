<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;
use Log;

class OspfIfStateChange implements SnmptrapHandler
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
        $ospfIfIpAddress = $trap->getOidData($trap->findOid('OSPF-MIB::ospfIfIpAddress'));
        $ospfPort = $device->ospfPorts()->where('ospfIfIpAddress', $ospfIfIpAddress)->first();

        $port = $device->ports()->where('port_id', $ospfPort->port_id)->first();

        if (! $port) {
            Log::warning("Snmptrap ospfIfStateChange: Could not find port at port_id $ospfPort->port_id for device: " . $device->hostname);

            return;
        }

        $ospfPort->ospfIfState = $trap->getOidData($trap->findOid('OSPF-MIB::ospfIfState'));

        $severity = match ($ospfPort->ospfIfState) {
            'down' => Severity::Error,
            'designatedRouter', 'backupDesignatedRouter', 'otherDesignatedRouter', 'pointToPoint' => Severity::Ok,
            'waiting', 'loopback' => Severity::Warning,
            default => Severity::Unknown,
        };

        $trap->log("OSPF interface $port->ifName is $ospfPort->ospfIfState", $severity);

        $ospfPort->save();
    }
}
