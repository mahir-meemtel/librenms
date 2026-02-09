<?php
namespace ObzoraNMS\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    // Redefine the exception so message defaults to a generic 'Invalid credentials'
    public function __construct($message = 'Invalid credentials', $hide_message = false, $code = 0, ?Exception $previous = null)
    {
        if ($hide_message) {
            $message = '';
        }

        parent::__construct($message, $code, $previous);
    }
}
