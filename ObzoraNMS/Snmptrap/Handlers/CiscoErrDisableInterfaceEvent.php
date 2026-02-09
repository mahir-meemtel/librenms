<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CiscoErrDisableInterfaceEvent implements SnmptrapHandler
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
        $ifIndex = explode('.', $trap->findOid('CISCO-ERR-DISABLE-MIB::cErrDisableIfStatusCause'));

        $port = $device->ports()->where('ifIndex', $ifIndex[1])->first();
        $cause = $trap->getOidData($trap->findOid('CISCO-ERR-DISABLE-MIB::cErrDisableIfStatusCause.' . $ifIndex[1] . '.0'));

        if (! $port) {
            $trap->log('SNMP TRAP: ' . $cause . ' error detected on unknown port. Either ifIndex is not found in the trap, or it does not match a port on this device.', Severity::Warning);

            return;
        }
        $trap->log('SNMP TRAP: ' . $cause . ' error detected on ' . $port->ifName . ' (Description: ' . $port->ifDescr . '). ' . $port->ifName . ' in err-disable state.', Severity::Warning);
    }
}
