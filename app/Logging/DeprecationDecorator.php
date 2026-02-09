<?php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class DeprecationDecorator extends LineFormatter
{
    public function format(LogRecord $record): string
    {
        return "\033[32;1m$record->message\033[0m\n";
    }
}
