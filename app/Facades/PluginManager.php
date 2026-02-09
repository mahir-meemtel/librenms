<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use ObzoraNMS\Interfaces\Plugins\PluginManagerInterface;

class PluginManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PluginManagerInterface::class;
    }
}
