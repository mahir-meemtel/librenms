<?php
namespace ObzoraNMS\Authentication;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Interfaces\Authentication\Authorizer;

class LegacyAuth
{
    protected static $_instance;
    private static $configToClassMap = [
        'mysql' => 'ObzoraNMS\Authentication\MysqlAuthorizer',
        'active_directory' => 'ObzoraNMS\Authentication\ActiveDirectoryAuthorizer',
        'ldap' => 'ObzoraNMS\Authentication\LdapAuthorizer',
        'radius' => 'ObzoraNMS\Authentication\RadiusAuthorizer',
        'http-auth' => 'ObzoraNMS\Authentication\HttpAuthAuthorizer',
        'ad-authorization' => 'ObzoraNMS\Authentication\ADAuthorizationAuthorizer',
        'ldap-authorization' => 'ObzoraNMS\Authentication\LdapAuthorizationAuthorizer',
        'sso' => 'ObzoraNMS\Authentication\SSOAuthorizer',
    ];

    /**
     * Gets the authorizer based on the config
     *
     * @return Authorizer
     */
    public static function get()
    {
        if (! static::$_instance) {
            $class = self::getClass();
            static::$_instance = new $class;
        }

        return static::$_instance;
    }

    /**
     * The auth mechanism type.
     *
     * @return mixed
     */
    public static function getType()
    {
        return ObzoraConfig::get('auth_mechanism');
    }

    /**
     * Get class for the given or current authentication type/mechanism
     *
     * @param  string  $type
     * @return string
     */
    public static function getClass($type = null)
    {
        if (is_null($type)) {
            $type = self::getType();
        }

        if (! isset(self::$configToClassMap[$type])) {
            throw new \RuntimeException($type . ' not found as auth_mechanism');
        }

        return self::$configToClassMap[$type];
    }

    /**
     * Destroy the existing instance and get a new one - required for tests.
     *
     * @return Authorizer
     */
    public static function reset()
    {
        static::$_instance = null;

        return static::get();
    }
}
