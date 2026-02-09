<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DeviceCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'device-cache';
    }
}
