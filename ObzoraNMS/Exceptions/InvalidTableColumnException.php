<?php
namespace ObzoraNMS\Exceptions;

class InvalidTableColumnException extends ApiException
{
    public function __construct(
        public readonly array $columns
    ) {
        parent::__construct('Invalid columns: ' . implode(',', $this->columns));
    }
}
