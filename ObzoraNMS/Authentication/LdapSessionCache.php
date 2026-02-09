<?php
namespace ObzoraNMS\Authentication;

use App\Facades\ObzoraConfig;
use Carbon\Carbon;
use Session;

trait LdapSessionCache
{
    protected function authLdapSessionCacheGet($attr)
    {
        $ttl = ObzoraConfig::get('auth_ldap_cache_ttl', 300);

        // no session, don't cache
        if (! class_exists('Session')) {
            return null;
        }

        // auth_ldap cache present in this session?
        if (! Session::has('auth_ldap')) {
            return null;
        }

        $cache = Session::get('auth_ldap');

        // $attr present in cache?
        if (! isset($cache[$attr])) {
            return null;
        }

        // Value still valid?
        if (time() - $cache[$attr]['last_updated'] >= $ttl) {
            return null;
        }

        return $cache[$attr]['value'];
    }

    protected function authLdapSessionCacheSet($attr, $value)
    {
        if (class_exists('Session')) {
            Session::put($attr, [
                'value' => $value,
                'last_updated' => Carbon::now(),
            ]);
        }
    }
}
