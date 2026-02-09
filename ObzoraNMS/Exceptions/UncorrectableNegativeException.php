<?php
namespace ObzoraNMS\Exceptions;

use Throwable;

class UncorrectableNegativeException extends \Exception
{
    public function __construct(string $message = 'Uncorrectable negative value', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
