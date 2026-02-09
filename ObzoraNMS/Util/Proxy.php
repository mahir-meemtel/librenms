<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;

class Proxy
{
    public static function http(): string
    {
        // use local_only to avoid CVE-2016-5385
        $http_proxy = getenv('http_proxy', local_only: true) ?: getenv('HTTP_PROXY', local_only: true) ?: ObzoraConfig::get('http_proxy', '');

        return $http_proxy;
    }

    public static function https(): string
    {
        // use local_only to avoid CVE-2016-5385
        return getenv('https_proxy', local_only: true) ?: getenv('HTTPS_PROXY', local_only: true) ?: ObzoraConfig::get('https_proxy', '');
    }

    public static function ignore(): array
    {
        // use local_only to avoid CVE-2016-5385
        $no_proxy = getenv('no_proxy', local_only: true) ?: getenv('NO_PROXY', local_only: true) ?: ObzoraConfig::get('no_proxy', '');

        if ($no_proxy == '') {
            return [];
        }

        return explode(',', str_replace(' ', '', $no_proxy));
    }
}
