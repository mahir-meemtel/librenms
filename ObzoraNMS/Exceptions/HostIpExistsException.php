<?php
namespace ObzoraNMS\Exceptions;

class HostIpExistsException extends HostExistsException
{
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var string
     */
    public $existing_hostname;
    /**
     * @var string
     */
    public $ip;

    public function __construct(string $hostname, string $existing_hostname, string $ip)
    {
        $this->hostname = $hostname;
        $this->existing_hostname = $existing_hostname;
        $this->ip = $ip;

        $message = trans('exceptions.host_exists.ip_exists', [
            'hostname' => $hostname,
            'existing' => $existing_hostname,
            'ip' => $ip,
        ]);

        parent::__construct($message);
    }
}
