<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Rrd extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ObzoraNMS\Data\Store\Rrd';
    }
}
