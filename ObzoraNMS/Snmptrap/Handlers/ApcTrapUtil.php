<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Snmptrap\Trap;

class ApcTrapUtil
{
    /**
     * Get the APC PDU Name
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getPduIdentName($trap)
    {
        return $trap->getOidData($trap->findOid('PowerNet-MIB::rPDUIdentName'));
    }

    /**
     * Get the APC PDU Phase Number
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getPduPhaseNum($trap)
    {
        return $trap->getOidData($trap->findOid('PowerNet-MIB::rPDULoadStatusPhaseNumber'));
    }

    /**
     * Get the APC Trap String
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getApcTrapString($trap)
    {
        return $trap->getOidData($trap->findOid('PowerNet-MIB::mtrapargsString'));
    }
}
