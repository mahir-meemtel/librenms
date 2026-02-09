<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Snmptrap\Trap;

class CyberPowerUtil
{
    /**
     * Get the trap message
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getMessage($trap)
    {
        return $trap->getOidData($trap->findOid('CPS-MIB::mtrapinfoString'));
    }
}
