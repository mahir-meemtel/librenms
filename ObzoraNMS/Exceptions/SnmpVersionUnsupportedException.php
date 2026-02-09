<?php
namespace ObzoraNMS\Exceptions;

class SnmpVersionUnsupportedException extends \Exception
{
    /**
     * @var string
     */
    public $snmpVersion;

    public function __construct(string $snmpVersion)
    {
        $this->snmpVersion = $snmpVersion;
        $message = trans('exceptions.snmp_version_unsupported.message', ['snmpver' => $snmpVersion]);

        parent::__construct($message);
    }
}
