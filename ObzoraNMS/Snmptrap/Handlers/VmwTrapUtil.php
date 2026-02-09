<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Snmptrap\Trap;

class VmwTrapUtil
{
    /**
     * Get the VMGuest hostname
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getGuestName($trap)
    {
        return $trap->getOidData($trap->findOid('VMWARE-VMINFO-MIB::vmwVmDisplayName'));
    }

    /**
     * Get the VMGuest ID number
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getGuestId($trap)
    {
        return $trap->getOidData($trap->findOid('VMWARE-VMINFO-MIB::vmwVmID'));
    }

    /**
     * Get the VMGuest configuration path
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getGuestConfigPath($trap)
    {
        return $trap->getOidData($trap->findOid('VMWARE-VMINFO-MIB::vmwVmConfigFilePath'));
    }
}
