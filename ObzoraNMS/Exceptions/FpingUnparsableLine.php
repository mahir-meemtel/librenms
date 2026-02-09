<?php
namespace ObzoraNMS\Exceptions;

class FpingUnparsableLine extends \Exception
{
    public function __construct(public readonly string $unparsedLine)
    {
        parent::__construct("Fping unparsable line: $unparsedLine");
    }
}
