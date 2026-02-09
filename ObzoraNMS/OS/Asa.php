<?php
namespace ObzoraNMS\OS;

class Asa extends Shared\Cisco
{
    // disable unsupported netstats
    public function pollIcmpNetstats(array $oids): array
    {
        return [];
    }

    public function pollIpNetstats(array $oids): array
    {
        return [];
    }

    public function pollUdpNetstats(array $oids): array
    {
        return [];
    }

    public function pollTcpNetstats(array $oids): array
    {
        return [];
    }
}
