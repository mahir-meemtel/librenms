<?php
namespace ObzoraNMS\Interfaces\Polling;

use ObzoraNMS\OS;

interface PollerModule
{
    public static function poll(OS $os);
}
