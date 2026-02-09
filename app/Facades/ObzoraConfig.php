<?php
namespace App\Facades;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;

class ObzoraConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'obzora-config';
    }

    public static function reload(): void
    {
        App::forgetInstance('obzora-config'); // clear singleton
        self::clearResolvedInstances(); // clear facade resolved instances cache
    }

    public static function invalidateAndReload(): void
    {
        self::invalidateCache();
        self::reload();

        Log::info('ObzoraNMS config cache cleared and config reloaded.');
    }
}
