<?php
namespace ObzoraNMS\Exceptions;

class ApiClientException extends \Exception
{
    /** @var array */
    private $output;

    /**
     * @param  string  $message
     * @param  array  $output
     */
    public function __construct($message = '', $output = [])
    {
        parent::__construct($message, 0, null);
        $this->output = $output;
    }

    public function getOutput(): array
    {
        return $this->output;
    }
}
