<?php
namespace App\Exceptions;

use Throwable;

class PluginDoesNotImplementHookException extends PluginException
{
    public function __construct(string $plugin, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Plugin ($plugin) does not implement hook.", $code, $previous);
    }
}
