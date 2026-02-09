<?php
namespace ObzoraNMS\Exceptions;

class HostUnreachableSnmpException extends HostUnreachableException
{
    /**
     * @var string
     */
    public $hostname;

    public function __construct(string $hostname)
    {
        $this->hostname = $hostname;
        $message = trans('exceptions.host_unreachable.unsnmpable', [
            'hostname' => $hostname,
        ]);
        parent::__construct($message);
    }
}
