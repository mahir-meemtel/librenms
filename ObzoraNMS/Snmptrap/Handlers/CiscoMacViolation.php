<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CiscoMacViolation implements SnmptrapHandler
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
        $ifName = $trap->getOidData($trap->findOid('IF-MIB::ifName'));
        $mac = $trap->getOidData($trap->findOid('CISCO-PORT-SECURITY-MIB::cpsIfSecureLastMacAddress'));

        $trap->log("SNMP Trap: Secure MAC Address Violation on port $ifName. Last MAC address: $mac", Severity::Warning);
    }
}
