<?php
namespace ObzoraNMS\Authentication;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Interfaces\Authentication\Authorizer;

abstract class AuthorizerBase implements Authorizer
{
    protected static $HAS_AUTH_USERMANAGEMENT = false;
    protected static $CAN_UPDATE_USER = false;
    protected static $CAN_UPDATE_PASSWORDS = false;
    protected static $AUTH_IS_EXTERNAL = false;

    public function canUpdatePasswords($username = '')
    {
        return static::$CAN_UPDATE_PASSWORDS;
    }

    public function canManageUsers()
    {
        return static::$HAS_AUTH_USERMANAGEMENT;
    }

    public function canUpdateUsers()
    {
        return static::$CAN_UPDATE_USER;
    }

    public function authIsExternal()
    {
        return static::$AUTH_IS_EXTERNAL;
    }

    public function getExternalUsername()
    {
        return $_SERVER[ObzoraConfig::get('http_auth_header')] ?? $_SERVER['PHP_AUTH_USER'] ?? null;
    }

    public function getRoles(string $username): array|false
    {
        return false; // return false don't update roles by default
    }
}
