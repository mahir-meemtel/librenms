<?php
namespace ObzoraNMS\Exceptions;

class HostUnreachableException extends \Exception
{
    protected $reasons = [];

    public function __toString()
    {
        $string = __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        foreach ($this->reasons as $reason) {
            $string .= "  $reason\n";
        }

        return $string;
    }

    /**
     * Add additional reasons
     *
     * @param  string  $snmpVersion
     * @param  string  $credentials
     */
    public function addReason(string $snmpVersion, string $credentials)
    {
        $vars = [
            'version' => $snmpVersion,
            'credentials' => $credentials,
        ];

        if ($snmpVersion == 'v3') {
            $this->reasons[] = trans('exceptions.host_unreachable.no_reply_credentials', $vars);
        } else {
            $this->reasons[] = trans('exceptions.host_unreachable.no_reply_community', $vars);
        }
    }

    /**
     * Get the reasons
     *
     * @return array
     */
    public function getReasons()
    {
        return $this->reasons;
    }
}
