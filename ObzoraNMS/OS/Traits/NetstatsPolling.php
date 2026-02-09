<?php
namespace ObzoraNMS\OS\Traits;

use SnmpQuery;

trait NetstatsPolling
{
    public function pollIcmpNetstats(array $oids): array
    {
        return SnmpQuery::get($oids)->values();
    }

    public function pollIpNetstats(array $oids): array
    {
        return SnmpQuery::get($oids)->values();
    }

    public function pollSnmpNetstats(array $oids): array
    {
        return SnmpQuery::get($oids)->values();
    }

    public function pollIpForwardNetstats(array $oids): array
    {
        return SnmpQuery::get($oids)->values();
    }

    public function pollUdpNetstats(array $oids): array
    {
        return SnmpQuery::get($oids)->values();
    }

    public function pollTcpNetstats(array $oids): array
    {
        return SnmpQuery::get($oids)->values();
    }
}
