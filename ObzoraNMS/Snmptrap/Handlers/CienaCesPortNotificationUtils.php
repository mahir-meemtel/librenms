<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use ObzoraNMS\Snmptrap\Trap;

class CienaCesPortNotificationUtils
{
    /**
     * Get Ciena Chassis ID
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getCienaChassis($trap)
    {
        if (str_starts_with($trap->getOidData($trap->findOid('SNMPv2-MIB::snmpTrapOID.0')), 'CIENA-CES-PORT-MIB')) {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-MIB::cienaCesChPortPgIdMappingChassisIndex'));
        } else {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-XCVR-MIB::cienaCesPortXcvrNotifChassisIndex'));
        }
    }

    /**
     * Get Ciena Shelf ID
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getCienaShelf($trap)
    {
        if (str_starts_with($trap->getOidData($trap->findOid('SNMPv2-MIB::snmpTrapOID.0')), 'CIENA-CES-PORT-MIB')) {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-MIB::cienaCesPortPgIdMappingShelfIndex'));
        } else {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-XCVR-MIB::cienaCesPortXcvrNotifShelfIndex'));
        }
    }

    /**
     * Get Ciena Slot ID
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getCienaSlot($trap)
    {
        if (str_starts_with($trap->getOidData($trap->findOid('SNMPv2-MIB::snmpTrapOID.0')), 'CIENA-CES-PORT-MIB')) {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-MIB::cienaCesChPortPgIdMappingNotifSlotIndex'));
        } else {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-XCVR-MIB::cienaCesPortXcvrNotifSlotIndex'));
        }
    }

    /**
     * Get Ciena Port ID
     *
     * @param  Trap  $trap
     * @return string
     */
    public static function getCienaPort($trap)
    {
        if (str_starts_with($trap->getOidData($trap->findOid('SNMPv2-MIB::snmpTrapOID.0')), 'CIENA-CES-PORT-MIB')) {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-MIB::cienaCesPortPgIdMappingNotifPortNumber'));
        } else {
            return $trap->getOidData($trap->findOid('CIENA-CES-PORT-XCVR-MIB::cienaCesPortXcvrNotifPortNumber'));
        }
    }
}
