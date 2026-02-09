<?php
namespace ObzoraNMS\Exceptions;

use ObzoraNMS\Util\IP;

class HostUnreachablePingException extends HostUnreachableException
{
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var string
     */
    public $ip;

    public function __construct(string $hostname)
    {
        $this->hostname = $hostname;
        $this->ip = gethostbyname($hostname);

        $message = trans('exceptions.host_unreachable.unpingable', [
            'hostname' => $hostname,
            'ip' => IP::isValid($this->ip) ? $this->ip : trans('exceptions.host_unreachable.unresolvable'),
        ]);

        parent::__construct($message);
    }
}
