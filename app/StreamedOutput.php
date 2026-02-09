<?php
namespace App;

use RuntimeException;
use Symfony\Component\Console\Output\StreamOutput;

class StreamedOutput extends StreamOutput
{
    protected function doWrite($message, $newline): void
    {
        if (false === @fwrite($this->getStream(), $message) || ($newline && (false === @fwrite($this->getStream(), PHP_EOL)))) {
            throw new RuntimeException('Unable to write output.');
        }

        echo $message . PHP_EOL;

        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }
}
