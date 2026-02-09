<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http as LaravelHttp;

class Http
{
    /**
     * Create a new client with proxy set if appropriate and a distinct User-Agent header
     */
    public static function client(): PendingRequest
    {
        return LaravelHttp::withOptions([
            'proxy' => [
                'http' => Proxy::http(),
                'https' => Proxy::https(),
                'no' => Proxy::ignore(),
            ],
        ])->withHeaders([
            'User-Agent' => ObzoraConfig::get('project_name') . '/' . Version::VERSION, // we don't need fine version here, just rough
        ]);
    }
}
