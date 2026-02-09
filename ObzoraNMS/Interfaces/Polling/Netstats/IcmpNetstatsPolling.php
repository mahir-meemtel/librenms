<?php
namespace ObzoraNMS\Interfaces\Polling\Netstats;

interface IcmpNetstatsPolling
{
    public function pollIcmpNetstats(array $oids): array;
}
