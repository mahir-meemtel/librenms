<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\OS;

class Packetlight extends OS
{
    /**
     * Subtract 30 (for yaml user_func)
     */
    public static function offsetSfpDbm($value)
    {
        return $value - 30;
    }

    /**
     * Subtract 128 (for yaml user_func)
     */
    public static function offsetSfpTemperature($value)
    {
        return $value - 128;
    }

    /**
     * Convert Watts 10e-7 to Dbm
     */
    public static function convertWattToDbm($value)
    {
        return 10 * log10($value / 10000000) + 30;
    }
}
