<?php
namespace App\Logging;

class NoColorFormatter extends CliColorFormatter
{
    protected bool $console = false;
}
