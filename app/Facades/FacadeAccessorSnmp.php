<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use ObzoraNMS\Data\Source\NetSnmpQuery;

class FacadeAccessorSnmp extends Facade
{
    protected static function getFacadeAccessor()
    {
        // always resolve a new instance
        self::clearResolvedInstance(NetSnmpQuery::class);

        return NetSnmpQuery::class;
    }
}
