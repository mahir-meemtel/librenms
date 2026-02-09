<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PortCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'port-cache';
    }
}
