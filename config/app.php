<?php
use Illuminate\Support\Facades\Facade;

return [

    'timezone' => ini_get('date.timezone') ?: 'UTC', // use existing timezone

    'default_locale' => 'en', // just a holder for the system set locale

    'aliases' => Facade::defaultAliases()->merge([
        'DeviceCache' => App\Facades\DeviceCache::class,
        'Permissions' => App\Facades\Permissions::class,
        'PortCache' => App\Facades\PortCache::class,
        'PluginManager' => App\Facades\PluginManager::class,
        'Rrd' => App\Facades\Rrd::class,
        'SnmpQuery' => App\Facades\FacadeAccessorSnmp::class,
        'ObzoraConfig' => App\Facades\ObzoraConfig::class,
    ])->forget([
        'Http', // don't use Laravel Http facade, ObzoraNMS has its own wrapper
    ])->toArray(),

    'charset' => 'UTF-8',

    'name' => 'ObzoraNMS',
];
