<?php
namespace ObzoraNMS\Interfaces\Polling\Netstats;

interface IpForwardNetstatsPolling
{
    public function pollIpForwardNetstats(array $oids): array;
}
