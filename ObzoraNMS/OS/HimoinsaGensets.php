<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\OS;

class HimoinsaGensets extends OS
{
    /* state parsing functions
        CEC7 (a.k.a CEA7CC2) bitmap status ( R G B T Mn Au Al)
        statusConm.0
        R = Mains commutator closed
        G = Gen commutator closed
        B = Blocked mode
        T = Test mode
        Mn = Manual mode
        Au = Auto mode
        Al = Active commutator alarm
        In ObzoraNMS it equals to 3 state sensors:
        Closed commutator: Mains or Genset (what commutator is closed, so where the power comes from)
        Genset Mode: Block, Test, Manual, Auto (the 4 modes of the genset)
        Alarm: Yes or No (If there is a commutator alarm)
        Example:
        Value = 66
        Value binary = 1000010
        States equals to:
        - Alarm: No active alarm
        - Genset Mode: Auto
        - Closed commutator: Mains

        CEA7 / CEM7 (CEA7 is a combination of CEC7 + CEM7 in a single Central) bitmap status (R G Al Bt B T Mn Au P A)
        status.0
        R = Mains commutator closed
        G = Gen commutator closed
        Al = Active Alarm
        Bt = Transfer Pump
        B = Blocked mode
        T = Test mode
        Mn = Manual mode
        Au = Auto mode
        P = Motor Stopped
        A = Motor Running
    */

    /**
     * @param  int  $value
     */
    public static function motorStatus($value): int
    {
        return ($value & 1) | ($value & 2);
    }

    /**
     * @param  int  $value
     */
    public static function modeStatus($value): int
    {
        return ($value & 4) | ($value & 8) | ($value & 16) | ($value & 32);
    }

    /**
     * @param  int  $value
     */
    public static function alarmStatus($value): int
    {
        return $value & 128;
    }

    /**
     * @param  int  $value
     */
    public static function transferPumpStatus($value): int
    {
        return $value & 64;
    }

    /**
     * @param  int  $value
     */
    public static function commStatus($value): int
    {
        return ($value & 512) | ($value & 256);
    }

    /**
     * @param  int  $value
     */
    public static function cec7CommStatus($value): int
    {
        return ($value & 32) | ($value & 64);
    }

    /**
     * @param  int  $value
     */
    public static function cec7CommAlarmStatus($value): int
    {
        return $value & 1;
    }

    /**
     * @param  int  $value
     */
    public static function cec7ModeStatus($value): int
    {
        return ($value & 2) | ($value & 4) | ($value & 8) | ($value & 16);
    }
}
