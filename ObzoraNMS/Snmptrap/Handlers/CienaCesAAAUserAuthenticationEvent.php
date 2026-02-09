<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class CienaCesAAAUserAuthenticationEvent implements SnmptrapHandler
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
        $user = $trap->getOidData($trap->findOid('CIENA-CES-AAA-MIB::cienaCesAAAUserName'));
        $message = $trap->getOidData($trap->findOid('CIENA-CES-AAA-MIB::cienaCesAAAUserAuthenticationDescription'));
        $severity = Severity::Notice;
        if ($trap->getOidData($trap->findOid('CIENA-CES-AAA-MIB::cienaCesAAAUserAuthenticationStatus')) == 'failure') {
            $severity = Severity::Warning;
        }
        $trap->log("Authentication attempt by $user. $message", $severity);
    }
}
