<?php
namespace ObzoraNMS\Util;

class Validate
{
    /**
     * Checks if the give string is a valid hostname
     *
     * @param  string  $hostname
     * @return bool
     */
    public static function hostname($hostname)
    {
        // The Internet standards (Request for Comments) for protocols mandate that
        // component hostname labels may contain only the ASCII letters 'a' through 'z'
        // (in a case-insensitive manner), the digits '0' through '9', and the hyphen
        // ('-'). The original specification of hostnames in RFC 952, mandated that
        // labels could not start with a digit or with a hyphen, and must not end with
        // a hyphen. However, a subsequent specification (RFC 1123) permitted hostname
        // labels to start with digits. No other symbols, punctuation characters, or
        // white space are permitted. While a hostname may not contain other characters,
        // such as the underscore character (_), other DNS names may contain the underscore
        // maximum length is 253 characters, maximum segment size is 63

        return
            preg_match("/^([a-z\d](-*[a-z\d_])*)(\.([a-z\d](-*[a-z\d_])*))*\.?$/i", $hostname) //valid chars check
            && preg_match('/^.{1,253}$/', $hostname) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*\.?$/", $hostname);
    }

    public static function ascDesc($direction, $default = 'ASC')
    {
        return in_array(strtolower($direction), ['asc', 'desc'], true)
            ? $direction
            : $default;
    }
}
