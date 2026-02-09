<?php
namespace ObzoraNMS\Interfaces\Discovery;

use ObzoraNMS\OS;

interface DiscoveryModule
{
    public static function runDiscovery(OS $os);
}
