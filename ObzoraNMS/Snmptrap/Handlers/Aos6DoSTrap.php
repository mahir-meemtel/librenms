<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class Aos6DoSTrap implements SnmptrapHandler
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
        $type = $trap->getOidData($trap->findOid('ALCATEL-IND1-IP-MIB::alaDoSType'));
        $detected = $trap->getOidData($trap->findOid('ALCATEL-IND1-IP-MIB::alaDoSDetected'));
        $ip = $trap->getOidData($trap->findOid('ALCATEL-IND1-IP-MIB::alaDoSIp'));
        $slot = $trap->getOidData($trap->findOid('ALCATEL-IND1-IP-MIB::alaDoSSlot'));
//        $port = $trap->getOidData($trap->findOid('ALCATEL-IND1-IP-MIB::alaDoSPort')); // unused
        $mac = $trap->getOidData($trap->findOid('ALCATEL-IND1-IP-MIB::alaDoSMac'));
        $trap->log("There has been detected a Denial of Service (DoS) attack. Type of the attack is: $type. Number of attacks are: $detected. Slot where was received is: $slot. Source IP is: $ip. Mac address is: $mac.");
    }
}
